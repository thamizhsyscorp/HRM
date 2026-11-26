<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('code', 30)->nullable()->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_employee_id')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('code', 30)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['department_id', 'name']);
        });

        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 30)->nullable()->unique();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('allowed_radius_meters')->default(100);
            $table->boolean('geofence_enabled')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('employee_code', 40)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('work_email', 190)->nullable()->unique();
            $table->string('personal_email', 190)->nullable();
            $table->string('phone', 30)->nullable()->index();
            $table->string('alternate_phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('work_location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('joining_date');
            $table->date('employment_end_date')->nullable();
            $table->string('employment_status', 30)->default('ACTIVE')->index();
            $table->string('employment_type', 30)->default('FULL_TIME');
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['department_id', 'employment_status']);
            $table->index(['manager_id', 'employment_status']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_employee_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['manager_employee_id']);
                }
            });
        }

        Schema::dropIfExists('employees');
        Schema::dropIfExists('work_locations');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};
