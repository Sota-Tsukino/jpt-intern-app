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
        Schema::table('users', function (Blueprint $table) {
            // nameカラムの長さを50文字に変更
            $table->string('name', 50)->change();

            // 役割（生徒/担任/管理者）
            $table->enum('role', ['student', 'teacher', 'admin'])->default('student')->after('password');

            // 所属クラスID（生徒・担任のみ、管理者はNULL）
            $table->foreignId('class_id')->nullable()->after('role')->constrained('classes')->onDelete('set null');

            // インデックス追加
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'class_id']);
            $table->string('name')->change();
        });
    }
};
