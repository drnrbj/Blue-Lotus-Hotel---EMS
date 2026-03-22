<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan make:migration add_extra_columns_to_payrolls_table
// then paste this content.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('basic_pay', 12, 2)->default(0)->after('total_hours');
            $table->enum('status', ['draft', 'released'])->default('draft')->after('net_pay');
            $table->timestamp('released_at')->nullable()->after('status');
            $table->foreignId('released_by')->nullable()->after('released_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['released_by']);
            $table->dropColumn(['basic_pay', 'status', 'released_at', 'released_by']);
        });
    }
};