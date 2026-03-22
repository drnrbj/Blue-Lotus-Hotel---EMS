<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();         // EMP001
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->restrictOnDelete();
            $table->string('position');
            $table->date('date_started');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract']);
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('manager_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employees'); }
};