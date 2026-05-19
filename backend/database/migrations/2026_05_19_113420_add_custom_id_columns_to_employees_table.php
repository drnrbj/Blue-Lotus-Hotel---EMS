<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add custom ID columns after the 'id' column
            $table->string('employee_code')->unique()->nullable()->after('id');
            $table->string('admin_code')->unique()->nullable()->after('employee_code');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['employee_code', 'admin_code']);
        });
    }
};