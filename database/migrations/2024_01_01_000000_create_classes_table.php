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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('grade')->comment('学年（1〜3）');
            $table->string('class_name', 10)->comment('クラス名（A, B）');
            $table->timestamps();

            // 同じ学年に同じクラス名は存在しない
            $table->unique(['grade', 'class_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
