<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')
                  ->constrained('training_programs')
                  ->cascadeOnDelete();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->cascadeOnDelete();
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
            $table->unique(['training_id', 'employee_id']);      // prevent duplicate entries
        });
    }
    public function down(): void { Schema::dropIfExists('training_participants'); }
};