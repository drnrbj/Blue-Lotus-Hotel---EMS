<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('last_working_day')->nullable()->after('status');
            $table->text('termination_reason')->nullable()->after('last_working_day');
            $table->timestamp('terminated_at')->nullable()->after('termination_reason');
            $table->softDeletes(); // adds deleted_at column
        });
    }
 
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['last_working_day', 'termination_reason', 'terminated_at']);
            $table->dropSoftDeletes();
        });
    }
};