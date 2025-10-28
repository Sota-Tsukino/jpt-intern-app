<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ENUMカラムに'sub_teacher'を追加
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'teacher', 'sub_teacher', 'admin') NOT NULL DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ENUMカラムから'sub_teacher'を削除
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student'");
    }
};
