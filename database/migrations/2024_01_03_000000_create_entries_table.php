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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('生徒ID');
            $table->date('entry_date')->comment('記録対象日（前登校日）');
            $table->timestamp('submitted_at')->comment('提出日時');
            $table->tinyInteger('health_status')->comment('体調（1〜5）');
            $table->tinyInteger('mental_status')->comment('メンタル（1〜5）');
            $table->string('study_reflection', 500)->comment('授業振り返り');
            $table->string('club_reflection', 500)->nullable()->comment('部活振り返り');
            $table->timestamps();

            // 同じ生徒が同じ記録対象日の記録を複数持たない
            $table->unique(['user_id', 'entry_date']);

            // インデックス追加
            $table->index('entry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
