<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->restrictOnDelete();
            $table->unsignedInteger('slots')->default(1);
            $table->date('posting_date');
            $table->date('deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'filled'])->default('open');
        });
    }
    public function down(): void { Schema::dropIfExists('job_postings'); }
};