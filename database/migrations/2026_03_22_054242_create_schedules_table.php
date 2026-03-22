<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->cascadeOnDelete();
            $table->time('shift_start');
            $table->time('shift_end');
            $table->string('days');                              // e.g. "Mon,Tue,Wed,Thu,Fri"
            $table->date('effective_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }
    public function down(): void { Schema::dropIfExists('schedules'); }
};