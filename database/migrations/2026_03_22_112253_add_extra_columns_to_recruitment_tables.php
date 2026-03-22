<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run: php artisan make:migration add_extra_columns_to_recruitment_tables
// then paste this content.

return new class extends Migration
{
    public function up(): void
    {
        // Add description to job_postings
        Schema::table('job_postings', function (Blueprint $table) {
            $table->text('description')->nullable()->after('status');
        });

        // Add email, phone, notes, job_posting_id to applicants
        Schema::table('applicants', function (Blueprint $table) {
            $table->foreignId('job_posting_id')->nullable()->after('id')
                  ->constrained('job_postings')->nullOnDelete();
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->text('notes')->nullable()->after('status');
        });

        // Add notes to interviews
        Schema::table('interviews', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['job_posting_id']);
            $table->dropColumn(['job_posting_id','email','phone','notes']);
        });
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};