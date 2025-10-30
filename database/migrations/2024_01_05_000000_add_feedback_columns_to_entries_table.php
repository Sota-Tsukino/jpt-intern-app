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
            $table->text('teacher_feedback')
                  ->nullable()
                  ->after('stamped_at')
                  ->comment('生徒へのコメント');

            $table->timestamp('commented_at')
                  ->nullable()
                  ->after('teacher_feedback')
                  ->comment('コメント日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['teacher_feedback', 'commented_at']);
        });
    }
};
