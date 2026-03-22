<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('applied_position');
            $table->date('applied_date');
            $table->enum('status', ['pending', 'shortlisted', 'hired', 'rejected'])->default('pending');
        });
    }
    public function down(): void { Schema::dropIfExists('applicants'); }
};