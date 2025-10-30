<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->foreignId('stamped_by')
                  ->nullable()
                  ->after('stamped_at')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('スタンプを押した教師ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['stamped_by']);
            $table->dropColumn('stamped_by');
        });
    }
};
