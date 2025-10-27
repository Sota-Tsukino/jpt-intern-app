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
            $table->enum('stamp_type', ['good', 'great', 'fighting', 'care'])
                  ->nullable()
                  ->after('club_reflection')
                  ->comment('スタンプ種類（既読処理時に必須）');

            $table->timestamp('stamped_at')
                  ->nullable()
                  ->after('stamp_type')
                  ->comment('スタンプ日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['stamp_type', 'stamped_at']);
        });
    }
};
