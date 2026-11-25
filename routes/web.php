<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\QrAttendanceController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (or login if guest)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dedicated Public/Terminal Attendance Daily QR API
Route::get('/api/attendance/daily-qr', [QrAttendanceController::class, 'getDailyQr'])->name('api.attendance.daily-qr');
Route::get('/api/attendance/daily-qr/image', [QrAttendanceController::class, 'getDailyQrImage'])->name('api.attendance.daily-qr.image');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    // Logout & Main Dashboard
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Attendance Management
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
    Route::get('/attendance/qr-kiosk', [QrAttendanceController::class, 'kiosk'])->name('attendance.qr-kiosk');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/qr-punch', [QrAttendanceController::class, 'punch'])->name('attendance.qr-punch');
    Route::post('/attendance/qr/regenerate', [QrAttendanceController::class, 'regenerate'])->name('attendance.qr.regenerate');
    Route::post('/api/attendance/qr-punch', [QrAttendanceController::class, 'punch'])->name('api.attendance.qr-punch');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Employee Management
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

    // Leave Management
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::post('/leaves/{leaveRequest}/review', [LeaveController::class, 'review'])->name('leaves.review');

    // Corporate Holidays & Calendar Schedule Management (Super Admin & HR)
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    // Payroll & Compensation
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');

    // Organization settings, roles, and permissions
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/departments', [SettingsController::class, 'storeDepartment'])->name('settings.departments.store');
    Route::put('/settings/departments/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.departments.update');
    Route::delete('/settings/departments/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.departments.destroy');
    Route::post('/settings/designations', [SettingsController::class, 'storeDesignation'])->name('settings.designations.store');
    Route::put('/settings/designations/{designation}', [SettingsController::class, 'updateDesignation'])->name('settings.designations.update');
    Route::delete('/settings/designations/{designation}', [SettingsController::class, 'destroyDesignation'])->name('settings.designations.destroy');
    Route::post('/settings/work-locations', [SettingsController::class, 'storeWorkLocation'])->name('settings.locations.store');
    Route::put('/settings/work-locations/{workLocation}', [SettingsController::class, 'updateWorkLocation'])->name('settings.locations.update');
    Route::delete('/settings/work-locations/{workLocation}', [SettingsController::class, 'destroyWorkLocation'])->name('settings.locations.destroy');
    Route::post('/settings/leave-types', [SettingsController::class, 'storeLeaveType'])->name('settings.leave-types.store');
    Route::put('/settings/leave-types/{leaveType}', [SettingsController::class, 'updateLeaveType'])->name('settings.leave-types.update');
    Route::delete('/settings/leave-types/{leaveType}', [SettingsController::class, 'destroyLeaveType'])->name('settings.leave-types.destroy');
    Route::post('/settings/roles', [SettingsController::class, 'storeRole'])->name('settings.roles.store');
    Route::delete('/settings/roles/{role}', [SettingsController::class, 'destroyRole'])->name('settings.roles.destroy');
    Route::put('/settings/roles/permissions', [SettingsController::class, 'updatePermissions'])->name('settings.roles.permissions');
});
