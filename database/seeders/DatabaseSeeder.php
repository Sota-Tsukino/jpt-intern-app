<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * 環境変数に応じて適切なSeederを実行:
     * - production/staging: ProductionSeeder（大量データ）
     * - その他（local等）: DevelopmentSeeder（少量データ）
     *
     * 手動で実行する場合:
     * - 開発用: php artisan db:seed --class=DevelopmentSeeder
     * - 本番用: php artisan db:seed --class=ProductionSeeder
     */
    public function run(): void
    {
        // 環境変数で切り替え
        $seederClass = in_array(config('app.env'), ['production', 'staging'])
            ? ProductionSeeder::class
            : DevelopmentSeeder::class;

        $this->command->info("実行するSeeder: {$seederClass}");
        $this->call($seederClass);
    }
}
