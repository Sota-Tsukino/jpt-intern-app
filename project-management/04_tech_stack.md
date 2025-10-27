# 技術スタック

## 1. フロントエンド

| カテゴリ | 技術 | バージョン | 用途 |
|---------|------|-----------|------|
| **マークアップ** | HTML5 | - | 基本構造 |
| **スタイル** | CSS3 | - | 基本スタイル |
| | Tailwind CSS | 3.x | ユーティリティファーストCSS |
| **スクリプト** | JavaScript | ES6+ | 文字数カウンター、パスワード生成等 |
| | Alpine.js | 3.x | 軽量JSフレームワーク（Breeze標準） |
| **グラフ描画** | Chart.js | 4.x | 推移グラフ表示 ★課題2追加 |

### 1.1 Tailwind CSSの利用
- Laravel Breeze標準で含まれる
- カスタムクラスは作成せず、ユーティリティクラスのみ使用
- レスポンシブ対応（ただしPoCではPC画面のみ）
- **【課題2提案】モバイル対応時はレスポンシブクラスを活用**

### 1.2 JavaScriptの利用シーン

#### 課題1（基本機能）
- リアルタイム文字数カウンター
- パスワード自動生成
- クリップボードコピー
- フォームバリデーション
- モーダル表示/非表示

#### 課題2追加（実装）
- **Chart.js による推移グラフ描画**
  - 折れ線グラフ（体調・メンタルの時系列推移）
  - 期間選択機能（1週間/1ヶ月/3ヶ月）
  - データ取得（Fetch API）
- **フラグUI の動的更新**
  - ラジオボタン選択時の表示切り替え
  - メモ欄の表示/非表示制御

#### 課題2提案（提案のみ）
- **クラス全体統計グラフ**（Chart.js）
  - 複数のグラフタイプ（折れ線、棒、積み上げ棒）
  - グラフ間の連動
- **通知機能のリアルタイム更新**
  - 既読処理の非同期通信
  - 未読カウントの動的更新

### 1.3 Chart.js 設定例（課題2 - 実装）
```javascript
// 個別生徒推移グラフ
const ctx = document.getElementById('progressChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,  // ['10/20', '10/21', ...]
        datasets: [
            {
                label: '体調',
                data: healthData,  // [3, 4, 3, ...]
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            },
            {
                label: 'メンタル',
                data: mentalData,  // [4, 3, 4, ...]
                borderColor: 'rgb(168, 85, 247)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                min: 1,
                max: 5,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
```

---

## 2. バックエンド

| カテゴリ | 技術 | バージョン | 備考 |
|---------|------|-----------|------|
| **言語** | PHP | 8.3 | |
| **フレームワーク** | Laravel | 10.x | LTS版 |
| **認証** | Laravel Breeze | 1.x | セッションベース認証 |

### 2.1 Laravelの主要機能

#### 課題1（基本機能）
- Eloquent ORM
- Blade テンプレートエンジン
- バリデーション
- ミドルウェア
- マイグレーション・Seeder
- Vite（アセットビルド）

#### 課題2追加（実装）
- **Policy（認可）**
  - NotebookPolicy: フラグ設定権限の制御
  - 学年内の教師のみフラグ設定可能
- **JSON レスポンス**
  - グラフ用データAPI
  - 非同期処理用エンドポイント

#### 課題2提案（提案のみ）
- **Laravel Command（バッチ処理）**
  - 体調異常の自動検知
  - メンタル異常の自動検知
  - 提出率低下の検知
- **Laravel Schedule（スケジューラ）**
  - 毎日朝8時に異常検知実行
  - 毎日夕方17時に提出率チェック
- **Queue（キュー）**
  - 重い処理の非同期実行（将来拡張）

### 2.2 Breezeで提供される機能
- ログイン / ログアウト
- パスワードリセット（管理者のみ）
- セッション管理
- CSRF保護
- Tailwind CSS統合
- Alpine.js統合

### 2.3 Policy 例（課題2 - 実装）
```php
// app/Policies/NotebookPolicy.php

class NotebookPolicy
{
    /**
     * フラグを設定できるか
     */
    public function updateFlag(User $user, Notebook $notebook)
    {
        // 担任または学年主任のみ
        if (!in_array($user->role, ['teacher', 'grade_leader'])) {
            return false;
        }
        
        // 同学年のみ
        return $user->grade_id === $notebook->user->grade_id;
    }
}
```

---

## 3. データベース

| カテゴリ | 技術 | バージョン | 備考 |
|---------|------|-----------|------|
| **RDBMS** | MySQL | 8.0 | |
| **文字セット** | utf8mb4 | - | 絵文字対応 |
| **照合順序** | utf8mb4_unicode_ci | - | |

### 3.1 データベース設定
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_notebook
DB_USERNAME=root
DB_PASSWORD=
```

### 3.2 テーブル構成

#### 課題1（基本テーブル）
- `users`: ユーザー情報
- `classes`: クラス情報
- `notebooks`: 連絡帳記録

#### 課題2追加（カラム拡張 - 実装）
- `notebooks` テーブルに8カラム追加
  - スタンプ関連（stamp_type, stamped_at）
  - コメント関連（teacher_feedback, commented_at）
  - フラグ関連（flag, flagged_at, flagged_by, flag_memo）

#### 課題2提案（新規テーブル - 提案のみ）
- `teacher_comments`: 担任間共有メモ
- `notifications`: システム内通知

---

## 4. 開発環境

| カテゴリ | 技術 | バージョン | 備考 |
|---------|------|-----------|------|
| **ローカルサーバー** | MAMP | 5.0.6 | PHP 8.3含む |
| **Webサーバー** | Apache | 2.4 | |
| **パッケージ管理** | Composer | 2.x | PHP依存関係 |
| | npm | 9.x+ | JS依存関係 |
| **ビルドツール** | Vite | 4.x | Laravel 10標準 |
| **バージョン管理** | Git | 2.x | |
| **リポジトリ** | GitLab | - | quest_1リポジトリ |

### 4.1 開発サーバー起動
```bash
# Laravel開発サーバー
php artisan serve

# Vite開発サーバー（別ターミナル）
npm run dev
```

### 4.2 課題2用のコマンド（提案のみ）
```bash
# 体調異常チェック（手動実行）
php artisan notebooks:check-health

# メンタル異常チェック
php artisan notebooks:check-mental

# 提出率チェック
php artisan notebooks:check-submission

# スケジューラ起動（cron）
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. 本番環境（AWS）

### 5.1 PoC用（最小構成）
```
EC2 (Laravel) ← HTTP直接アクセス
  ↓
RDS (MySQL 8.0)
```

| サービス | スペック | 用途 |
|---------|---------|------|
| **EC2** | t2.micro | Webサーバー（Laravel） |
| **RDS** | db.t3.micro (MySQL 8.0) | データベース |

### 5.2 本番想定（課題2提案）
```
Route53 (DNS)
  ↓
ELB (SSL終端 ACM)
  ↓
EC2 (Laravel)
  ↓
RDS (MySQL 8.0)

S3 (ログ、バックアップ)
CloudWatch (モニタリング)
```

| サービス | 用途 |
|---------|------|
| **VPC** | ネットワーク分離 |
| **Route53** | DNS管理 |
| **ELB** | ロードバランサー |
| **ACM** | SSL証明書 |
| **EC2** | Webサーバー |
| **RDS** | データベース |
| **S3** | 静的ファイル、バックアップ |
| **CloudWatch** | ログ・メトリクス監視 |

### 5.3 EC2環境構築
```bash
# Ubuntu 24.04 LTS

# PHP 8.3インストール
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl

# Composerインストール
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Apacheインストール・設定
sudo apt install -y apache2
sudo a2enmod rewrite
sudo systemctl restart apache2

# Laravelデプロイ
cd /var/www
git clone [リポジトリURL] laravel_notebook
cd laravel_notebook
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed
```

### 5.4 課題2用の追加設定（提案のみ）
```bash
# Cronジョブ設定（スケジューラ）
crontab -e

# 以下を追加
* * * * * cd /var/www/laravel_notebook && php artisan schedule:run >> /dev/null 2>&1

# ログローテーション設定
sudo nano /etc/logrotate.d/laravel

# 以下を追加
/var/www/laravel_notebook/storage/logs/*.log {
    daily
    rotate 14
    compress
    missingok
    notifempty
}
```

---

## 6. 開発ツール

| カテゴリ | ツール | 用途 |
|---------|-------|------|
| **エディタ** | VS Code | コーディング |
| | Claude Code | AI支援コーディング |
| **デザインツール** | Figma | 画面遷移図作成 |
| **ターミナル** | - | コマンド実行 |
| **ブラウザ** | Microsoft Edge | 動作確認（必須） |
| **データベース管理** | phpMyAdmin | MAMP標準 |

---

## 7. 依存パッケージ

### 7.1 Composer（PHP）

#### 課題1
```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^10.0",
        "laravel/breeze": "^1.0"
    }
}
```

#### 課題2追加（提案のみ）
```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^10.0",
        "laravel/breeze": "^1.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    }
}
```

### 7.2 npm（JavaScript）

#### 課題1
```json
{
    "devDependencies": {
        "alpinejs": "^3.0",
        "tailwindcss": "^3.0",
        "vite": "^4.0",
        "laravel-vite-plugin": "^0.8"
    }
}
```

#### 課題2追加（実装）
```json
{
    "dependencies": {
        "chart.js": "^4.4.0"
    },
    "devDependencies": {
        "alpinejs": "^3.0",
        "tailwindcss": "^3.0",
        "vite": "^4.0",
        "laravel-vite-plugin": "^0.8"
    }
}
```

### 7.3 Chart.js インストール（課題2 - 実装）
```bash
# Chart.js インストール
npm install chart.js

# Vite設定に追加
# vite.config.js は自動で認識
```

---

## 8. セキュリティ

| 項目 | 対策 | 実装 |
|------|------|------|
| **認証** | セッションベース認証 | Laravel Breeze |
| **パスワード** | bcryptハッシュ化 | Laravel標準 |
| **CSRF保護** | トークン検証 | Laravel標準 |
| **XSS対策** | エスケープ処理 | Blade `{{ }}` |
| **SQLインジェクション** | プリペアドステートメント | Eloquent ORM |
| **セッション管理** | セキュアクッキー | Laravel標準 |
| **認可** | Policy | Laravel標準 ★課題2追加 |

### 8.1 課題2追加のセキュリティ対策

#### 認可（Policy）
```php
// フラグ設定権限チェック
$this->authorize('updateFlag', $notebook);

// 通知閲覧権限チェック
if ($notification->user_id !== auth()->id()) {
    abort(403);
}
```

#### JSON API のセキュリティ
```php
// CSRF トークン検証（POST/PATCH/DELETE）
@csrf

// JSONレスポンスのエスケープ
return response()->json([
    'data' => e($data)  // XSS対策
]);
```

---

## 9. パフォーマンス

### 9.1 最適化施策

#### 課題1（基本）
- Eloquentの遅延ロード回避（`with()`）
- ページネーション（10件/ページ）
- インデックス設定（`record_date`, `is_read` 等）

#### 課題2追加（実装）
- **フラグ検索の最適化**
  - `flag` カラムにインデックス
  - `flagged_at` カラムにインデックス
- **グラフデータ取得の最適化**
  - 必要なカラムのみ SELECT
  - 日付範囲の絞り込み
```php
// 最適化例
Notebook::select('record_date', 'health_status', 'mental_status')
    ->where('user_id', $studentId)
    ->whereBetween('record_date', [$startDate, $endDate])
    ->orderBy('record_date')
    ->get();
```

#### 課題2提案（提案のみ）
- **通知テーブルのパージ処理**
  - 3ヶ月以上前の既読通知を自動削除
- **統計データのキャッシュ**
  - クラス統計グラフのデータをキャッシュ（1時間）

### 9.2 キャッシュ（本番のみ）
```bash
# 設定キャッシュ
php artisan config:cache

# ルートキャッシュ
php artisan route:cache

# ビューキャッシュ
php artisan view:cache

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 10. モニタリング・ログ

### 10.1 ログ

#### 課題1
- **場所**: `storage/logs/laravel.log`
- **レベル**: debug, info, warning, error

#### 課題2追加（提案のみ）
- **バッチ処理ログ**
  - 体調異常検知の実行ログ
  - 通知作成のログ
```php
// app/Console/Commands/CheckHealthAnomalies.php
Log::info('体調異常チェック開始');
Log::info("体調異常検出: {$student->name}");
Log::info("体調異常チェック完了: {$count}件");
```

### 10.2 エラーハンドリング
```php
// app/Exceptions/Handler.php

public function register()
{
    $this->reportable(function (Throwable $e) {
        \Log::error($e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
        ]);
    });
}
```

### 10.3 課題2用のモニタリング（提案のみ）
```php
// CloudWatch Logs へのログ送信（将来拡張）
// AWS SDK for PHP を使用

use Aws\CloudWatchLogs\CloudWatchLogsClient;

$client = new CloudWatchLogsClient([
    'region' => 'ap-northeast-1',
    'version' => 'latest'
]);

$client->putLogEvents([
    'logGroupName' => '/aws/laravel/notebook',
    'logStreamName' => 'application',
    'logEvents' => [
        [
            'message' => '体調異常検知実行',
            'timestamp' => time() * 1000
        ]
    ]
]);
```

---

## 11. テスト

### 11.1 テスト環境

| カテゴリ | 技術 | 備考 |
|---------|------|------|
| **ツール** | PHPUnit | Laravel標準 |
| **データベース** | SQLite | in-memory |

### 11.2 テストコマンド
```bash
# 全テスト実行
php artisan test

# 特定のテスト
php artisan test --filter NotebookTest

# カバレッジレポート
php artisan test --coverage
```

### 11.3 テスト例（課題2 - 実装）
```php
// tests/Feature/FlagTest.php

class FlagTest extends TestCase
{
    public function test_teacher_can_set_flag()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $notebook = Notebook::factory()->create();
        
        $response = $this->actingAs($teacher)
            ->patch(route('teacher.notebooks.updateFlag', $notebook), [
                'flag' => 'watch',
                'flag_memo' => 'テストメモ'
            ]);
        
        $response->assertRedirect();
        $this->assertEquals('watch', $notebook->fresh()->flag);
    }
}
```

---

## 12. 課題1→課題2 技術スタック変更サマリー

### 12.1 新規追加（実装）
```
✅ Chart.js 4.x
   - npm install chart.js
   - 推移グラフ描画用

✅ Laravel Policy
   - フラグ設定権限の制御
   - NotebookPolicy 作成

✅ JSON API エンドポイント
   - グラフデータ取得用
   - 非同期処理用
```

### 12.2 新規追加（提案のみ）
```
📄 Laravel Command
   - 体調異常検知
   - メンタル異常検知
   - 提出率チェック

📄 Laravel Schedule
   - Cronジョブ設定
   - 毎日自動実行

📄 Laravel Queue（将来拡張）
   - 重い処理の非同期実行
```

### 12.3 変更なし
```
- Laravel 10.x
- PHP 8.3
- MySQL 8.0
- Tailwind CSS
- Alpine.js
- Laravel Breeze
```

---

## 13. ビルド・デプロイ手順

### 13.1 開発環境
```bash
# 1. リポジトリクローン
git clone [リポジトリURL]
cd laravel_notebook

# 2. 依存関係インストール
composer install
npm install

# 3. 環境設定
cp .env.example .env
php artisan key:generate

# 4. データベース作成
# MAMPでデータベース作成
# データベース名: laravel_notebook

# 5. マイグレーション
php artisan migrate
php artisan db:seed

# 6. 開発サーバー起動
php artisan serve
npm run dev  # 別ターミナル
```

### 13.2 本番環境（AWS）
```bash
# 1. EC2にSSH接続
ssh -i key.pem ubuntu@[EC2_IP]

# 2. リポジトリクローン
cd /var/www
sudo git clone [リポジトリURL] laravel_notebook
cd laravel_notebook

# 3. 本番用インストール
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 4. 環境設定
cp .env.production .env
php artisan key:generate

# 5. データベース設定（RDS）
# .env ファイルでRDS接続情報を設定

# 6. マイグレーション
php artisan migrate --force
php artisan db:seed --force

# 7. パーミッション設定
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 8. キャッシュ生成
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Apache設定
sudo systemctl restart apache2
```

### 13.3 課題2用の追加手順（提案のみ）
```bash
# Cronジョブ設定
crontab -e

# 以下を追加
* * * * * cd /var/www/laravel_notebook && php artisan schedule:run >> /dev/null 2>&1

# スケジューラの動作確認
php artisan schedule:list

# バッチ処理の手動実行テスト
php artisan notebooks:check-health
php artisan notebooks:check-mental
php artisan notebooks:check-submission
```

