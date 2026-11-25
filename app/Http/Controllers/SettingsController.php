<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Designation;
use App\Models\LeaveType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\WorkLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const SECTIONS = ['departments', 'designations', 'locations', 'leave-types', 'roles'];

    private const SYSTEM_ROLES = ['super-admin', 'hr-manager', 'employee'];

    public function index(Request $request): View
    {
        $this->authorizeSettings();

        $isSuperAdmin = Auth::user()->hasRole('super-admin');
        $section = $request->string('section')->toString();
        if (! in_array($section, self::SECTIONS, true) || ($section === 'roles' && ! $isSuperAdmin)) {
            $section = 'departments';
        }

        $departments = Department::withCount(['employees', 'designations'])->orderBy('name')->get();
        $designations = Designation::with('department')->withCount('employees')->orderBy('name')->get();
        $locations = WorkLocation::withCount('employees')->orderBy('name')->get();
        $leaveTypes = LeaveType::withCount('requests')->orderBy('name')->get();
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('settings.index', compact(
            'section',
            'isSuperAdmin',
            'departments',
            'designations',
            'locations',
            'leaveTypes',
            'roles',
            'permissions'
        ));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:departments,name'],
            'code' => ['nullable', 'string', 'max:30', 'unique:departments,code'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $department = Department::create([
            'name' => $validated['name'],
            'code' => $this->code($validated['code'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'DEPARTMENT_CREATED', $department);

        return $this->backTo('departments', "Department {$department->name} created.");
    }

    public function updateDepartment(Request $request, Department $department): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:departments,name,'.$department->id],
            'code' => ['nullable', 'string', 'max:30', 'unique:departments,code,'.$department->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $department->update([
            'name' => $validated['name'],
            'code' => $this->code($validated['code'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'DEPARTMENT_UPDATED', $department);

        return $this->backTo('departments', "Department {$department->name} updated.");
    }

    public function destroyDepartment(Request $request, Department $department): RedirectResponse
    {
        $this->authorizeSettings();

        if ($department->employees()->exists() || $department->designations()->exists()) {
            return $this->backTo('departments', 'Move people and designations out of this department before deleting it.', true);
        }

        $name = $department->name;
        $this->audit($request, 'DEPARTMENT_DELETED', $department);
        $department->delete();

        return $this->backTo('departments', "Department {$name} removed.");
    }

    public function storeDesignation(Request $request): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'code' => ['nullable', 'string', 'max:30', 'unique:designations,code'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $exists = Designation::where('department_id', $validated['department_id'] ?? null)
            ->where('name', $validated['name'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'This designation already exists in that department.'])->withInput();
        }

        $designation = Designation::create([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
            'code' => $this->code($validated['code'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'DESIGNATION_CREATED', $designation);

        return $this->backTo('designations', "Designation {$designation->name} created.");
    }

    public function updateDesignation(Request $request, Designation $designation): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'code' => ['nullable', 'string', 'max:30', 'unique:designations,code,'.$designation->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $duplicate = Designation::where('department_id', $validated['department_id'] ?? null)
            ->where('name', $validated['name'])
            ->where('id', '!=', $designation->id)
            ->exists();
        if ($duplicate) {
            return back()->withErrors(['name' => 'This designation already exists in that department.'])->withInput();
        }

        $designation->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
            'code' => $this->code($validated['code'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'DESIGNATION_UPDATED', $designation);

        return $this->backTo('designations', "Designation {$designation->name} updated.");
    }

    public function destroyDesignation(Request $request, Designation $designation): RedirectResponse
    {
        $this->authorizeSettings();

        if ($designation->employees()->exists()) {
            return $this->backTo('designations', 'Reassign employees before deleting this designation.', true);
        }

        $name = $designation->name;
        $this->audit($request, 'DESIGNATION_DELETED', $designation);
        $designation->delete();

        return $this->backTo('designations', "Designation {$name} removed.");
    }

    public function storeWorkLocation(Request $request): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30', 'unique:work_locations,code'],
            'address' => ['nullable', 'string', 'max:500'],
            'allowed_radius_meters' => ['nullable', 'integer', 'min:10', 'max:5000'],
        ]);

        $location = WorkLocation::create([
            'name' => $validated['name'],
            'code' => $this->code($validated['code'] ?? null),
            'address' => $validated['address'] ?? null,
            'allowed_radius_meters' => $validated['allowed_radius_meters'] ?? 100,
            'geofence_enabled' => $request->boolean('geofence_enabled'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'WORK_LOCATION_CREATED', $location);

        return $this->backTo('locations', "Work location {$location->name} created.");
    }

    public function updateWorkLocation(Request $request, WorkLocation $workLocation): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30', 'unique:work_locations,code,'.$workLocation->id],
            'address' => ['nullable', 'string', 'max:500'],
            'allowed_radius_meters' => ['nullable', 'integer', 'min:10', 'max:5000'],
        ]);

        $workLocation->update([
            'name' => $validated['name'],
            'code' => $this->code($validated['code'] ?? null),
            'address' => $validated['address'] ?? null,
            'allowed_radius_meters' => $validated['allowed_radius_meters'] ?? 100,
            'geofence_enabled' => $request->boolean('geofence_enabled'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'WORK_LOCATION_UPDATED', $workLocation);

        return $this->backTo('locations', "Work location {$workLocation->name} updated.");
    }

    public function destroyWorkLocation(Request $request, WorkLocation $workLocation): RedirectResponse
    {
        $this->authorizeSettings();

        if ($workLocation->employees()->exists()) {
            return $this->backTo('locations', 'Reassign employees before deleting this work location.', true);
        }

        $name = $workLocation->name;
        $this->audit($request, 'WORK_LOCATION_DELETED', $workLocation);
        $workLocation->delete();

        return $this->backTo('locations', "Work location {$name} removed.");
    }

    public function storeLeaveType(Request $request): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:leave_types,name'],
            'code' => ['required', 'string', 'max:30', 'unique:leave_types,code'],
            'annual_quota' => ['required', 'numeric', 'min:0', 'max:365'],
        ]);

        $leaveType = LeaveType::create([
            'name' => $validated['name'],
            'code' => $this->code($validated['code']),
            'annual_quota' => $validated['annual_quota'],
            'is_paid' => $request->boolean('is_paid'),
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'LEAVE_TYPE_CREATED', $leaveType);

        return $this->backTo('leave-types', "Leave type {$leaveType->name} created.");
    }

    public function updateLeaveType(Request $request, LeaveType $leaveType): RedirectResponse
    {
        $this->authorizeSettings();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:leave_types,name,'.$leaveType->id],
            'code' => ['required', 'string', 'max:30', 'unique:leave_types,code,'.$leaveType->id],
            'annual_quota' => ['required', 'numeric', 'min:0', 'max:365'],
        ]);

        $leaveType->update([
            'name' => $validated['name'],
            'code' => $this->code($validated['code']),
            'annual_quota' => $validated['annual_quota'],
            'is_paid' => $request->boolean('is_paid'),
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit($request, 'LEAVE_TYPE_UPDATED', $leaveType);

        return $this->backTo('leave-types', "Leave type {$leaveType->name} updated.");
    }

    public function destroyLeaveType(Request $request, LeaveType $leaveType): RedirectResponse
    {
        $this->authorizeSettings();

        if ($leaveType->requests()->exists()) {
            return $this->backTo('leave-types', 'This leave type already has requests, so it can only be turned off.', true);
        }

        $name = $leaveType->name;
        $this->audit($request, 'LEAVE_TYPE_DELETED', $leaveType);
        $leaveType->delete();

        return $this->backTo('leave-types', "Leave type {$name} removed.");
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $this->authorizeRoles();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = Str::slug($validated['name']);
        if ($slug === '' || Role::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'Choose a different role name.'])->withInput();
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $this->audit($request, 'ROLE_CREATED', $role);

        return $this->backTo('roles', "Role {$role->name} created. Turn on the permissions it should have.");
    }

    public function destroyRole(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRoles();

        if (in_array($role->slug, self::SYSTEM_ROLES, true)) {
            return $this->backTo('roles', 'Built-in roles stay in place. Change their permissions instead.', true);
        }

        if ($role->users()->exists()) {
            return $this->backTo('roles', 'Reassign people before deleting this role.', true);
        }

        $name = $role->name;
        $this->audit($request, 'ROLE_DELETED', $role);
        $role->delete();

        return $this->backTo('roles', "Role {$name} removed.");
    }

    public function updatePermissions(Request $request): RedirectResponse
    {
        $this->authorizeRoles();

        $validated = $request->validate([
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'grants' => ['nullable', 'array'],
            'grants.*' => ['array'],
            'grants.*.*' => ['integer', 'exists:permissions,id'],
        ]);

        $locked = Permission::whereIn('slug', ['manage-roles', 'manage-settings'])->pluck('id');

        foreach ($validated['role_ids'] as $roleId) {
            $role = Role::find($roleId);
            if (! $role) {
                continue;
            }

            $granted = collect($validated['grants'][$roleId] ?? [])->map(fn ($id) => (int) $id);
            if ($role->slug === 'super-admin') {
                $granted = $granted->merge($locked)->unique();
            }

            $role->permissions()->sync($granted->all());
        }

        $this->audit($request, 'PERMISSIONS_UPDATED', Auth::user());

        return $this->backTo('roles', 'Role permissions saved.');
    }

    private function authorizeSettings(): void
    {
        $user = Auth::user();
        if (! $user->hasRole('super-admin') && ! $user->hasRole('hr-manager')) {
            abort(403, 'Only HR and Super Admin can open settings.');
        }
    }

    private function authorizeRoles(): void
    {
        if (! Auth::user()->hasRole('super-admin')) {
            abort(403, 'Only a super admin can change roles and permissions.');
        }
    }

    private function code(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : Str::upper($value);
    }

    private function backTo(string $section, string $message, bool $error = false): RedirectResponse
    {
        $redirect = redirect()->route('settings.index', ['section' => $section]);

        return $error ? $redirect->with('status', $message) : $redirect->with('success', $message);
    }

    private function audit(Request $request, string $action, object $model): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->id,
            'new_values' => method_exists($model, 'toArray') ? $model->toArray() : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
}
