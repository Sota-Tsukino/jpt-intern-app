<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * 本番用/デモ用Seeder（大量データ）
     *
     * データ量:
     * - クラス: 6クラス
     * - 管理者: 1人
     * - 担任: 6人（各クラス1名）
     * - 生徒: 180人（各クラス30名）
     * - 連絡帳: 約550件
     *   - 生徒1人（デモ用）: 30件（推移グラフ確認用）
     *   - その他の生徒: 3件ずつ
     *
     * 所要時間: 約30秒〜1分
     */
    public function run(): void
    {
        // 1. クラス作成
        $this->call(ClassSeeder::class);

        // 2. ユーザー作成（各クラス30名）
        $this->call(UserSeeder::class, false, ['studentCountPerClass' => 30]);

        // 3. 連絡帳作成（1人目30件、残り3件ずつ）
        $this->call(EntrySeeder::class, false, [
            'firstStudentEntryCount' => 30,
            'otherStudentEntryCount' => 3,
        ]);
    }
}
