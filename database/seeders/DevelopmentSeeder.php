<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * 開発用Seeder（少量データで高速実行）
     *
     * データ量:
     * - クラス: 6クラス
     * - 管理者: 1人
     * - 担任: 6人（各クラス1名）
     * - 生徒: 18人（各クラス3名）
     * - 連絡帳: 54件（各生徒3件）
     *
     * 所要時間: 約5秒
     */
    public function run(): void
    {
        // 1. クラス作成
        $this->call(ClassSeeder::class);

        // 2. ユーザー作成（各クラス3名）
        $this->call(UserSeeder::class, false, ['studentCountPerClass' => 3]);

        // 3. 連絡帳作成（全員3件ずつ）
        $this->call(EntrySeeder::class, false, [
            'firstStudentEntryCount' => 3,
            'otherStudentEntryCount' => 3,
        ]);
    }
}
