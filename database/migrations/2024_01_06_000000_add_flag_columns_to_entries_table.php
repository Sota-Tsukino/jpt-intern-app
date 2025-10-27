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
            $table->enum('flag', ['none', 'watch', 'urgent'])
                  ->default('none')
                  ->after('commented_at')
                  ->comment('注目フラグ');

            $table->timestamp('flagged_at')
                  ->nullable()
                  ->after('flag')
                  ->comment('フラグ設定日時');

            $table->foreignId('flagged_by')
                  ->nullable()
                  ->after('flagged_at')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('フラグ設定者ID');

            $table->text('flag_memo')
                  ->nullable()
                  ->after('flagged_by')
                  ->comment('フラグメモ（気づきメモ）');

            // インデックス追加
            $table->index('flag');
            $table->index('flagged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['flagged_by']);
            $table->dropIndex(['flag']);
            $table->dropIndex(['flagged_at']);
            $table->dropColumn(['flag', 'flagged_at', 'flagged_by', 'flag_memo']);
        });
    }
};
