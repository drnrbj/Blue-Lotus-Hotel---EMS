<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();
            $table->foreignId('interviewer_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->dateTime('schedule_date');
            $table->enum('status', ['scheduled', 'done', 'cancelled'])->default('scheduled');
        });
    }
    public function down(): void { Schema::dropIfExists('interviews'); }
};