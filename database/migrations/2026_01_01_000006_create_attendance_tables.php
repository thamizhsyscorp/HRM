<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->timestamp('first_check_in_at')->nullable();
            $table->timestamp('last_check_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->string('status', 30)->default('PENDING')->index();
            $table->boolean('is_manual')->default(false);
            $table->text('admin_remarks')->nullable();
            $table->foreignId('modified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employee_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
            $table->index(['employee_id', 'attendance_date']);
        });

        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 30); // CHECK_IN, CHECK_OUT, BREAK_START, BREAK_END, MANUAL
            $table->timestamp('recorded_at');
            $table->string('photo_path')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('location_accuracy_meters', 8, 2)->nullable();
            $table->unsignedInteger('distance_from_office_meters')->nullable();
            $table->boolean('within_geofence')->nullable();
            $table->string('source', 30)->default('WEB');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'recorded_at']);
            $table->index(['attendance_id', 'event_type']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
        Schema::dropIfExists('attendances');
    }
};
