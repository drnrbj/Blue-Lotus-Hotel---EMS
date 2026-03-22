<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();  // e.g. 8.50
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->unique(['employee_id', 'date']);             // one record per employee per day
        });
    }
    public function down(): void { Schema::dropIfExists('attendance'); }
};