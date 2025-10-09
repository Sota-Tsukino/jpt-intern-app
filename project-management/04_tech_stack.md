# 技術スタック

## 1. フロントエンド

| カテゴリ | 技術 | バージョン | 用途 |
|---------|------|-----------|------|
| **マークアップ** | HTML5 | - | 基本構造 |
| **スタイル** | CSS3 | - | 基本スタイル |
| | Tailwind CSS | 3.x | ユーティリティファーストCSS |
| **スクリプト** | JavaScript | ES6+ | 文字数カウンター、パスワード生成等 |
| | Alpine.js | 3.x | 軽量JSフレームワーク（Breeze標準） |

### 1.1 Tailwind CSSの利用
- Laravel Breeze標準で含まれる
- カスタムクラスは作成せず、ユーティリティクラスのみ使用
- レスポンシブ対応（ただしPoCではPC画面のみ）

### 1.2 JavaScriptの利用シーン
- リアルタイム文字数カウンター
- パスワード自動生成
- クリップボードコピー
- フォームバリデーション
- モーダル表示/非表示

---

## 2. バックエンド

| カテゴリ | 技術 | バージョン | 備考 |
|---------|------|-----------|------|
| **言語** | PHP | 8.3 | |
| **フレームワーク** | Laravel | 10.x | LTS版 |
| **認証** | Laravel Breeze | 1.x | セッションベース認証 |

### 2.1 Laravelの主要機能
- Eloquent ORM
- Blade テンプレートエンジン
- バリデーション
- ミドルウェア
- マイグレーション・Seeder
- Vite（アセットビルド）

### 2.2 Breezeで提供される機能
- ログイン / ログアウト
- パスワードリセット
- セッション管理
- CSRF保護
- Tailwind CSS統合
- Alpine.js統合

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

カテゴリ技術バージョン備考ローカルサーバーMAMP5.0.6PHP 8.3含むWebサーバーApache2.4パッケージ管理Composer2.xPHP依存関係npm9.x+JS依存関係ビルドツールVite4.xLaravel 10標準バージョン管理Git2.xリポジトリGitLab-quest_1リポジトリ


### 4.1 開発サーバー起動
```bash
# Laravel開発サーバー
php artisan serve

# Vite開発サーバー（別ターミナル）
npm run dev
```

## 5. 本番環境（AWS）仮Ver
<!-- 後程整理 -->
### 5.1 インフラ構成
PoC用（最小構成）
```
EC2 (Laravel) ← HTTP直接アクセス
  ↓
RDS (MySQL 8.0)
```


| サービス | スペック | 用途 |
|---------|---------|------|
| **EC2** | t2.micro | Webサーバー（Laravel） |
| **RDS** | db.t3.micro (MySQL 8.0) | データベース |


本番想定（プレゼンで提案）
```
Route53 (DNS)
  ↓
ELB (SSL終端 ACM)
  ↓
EC2 (Laravel)
  ↓
RDS (MySQL 8.0)

S3 (ログ、バックアップ)
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

### 5.2 EC2環境構築
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

## 6. 開発ツール
| カテゴリ | ツール | 用途 |
|---------|-------|------|
| **エディタ** | - | VS Code等 |
| **デザインツール** | Figma | 画面遷移図作成 |
| **AI補助** | Claude Code | コーディング支援 |
| **ターミナル** | - | コマンド実行 |

## 7. 依存パッケージ
7.1 Composer（PHP）
```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^10.0",
        "laravel/breeze": "^1.0"
    }
}
```

7.2 npm（JavaScript）
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
## 8. セキュリティ
| 項目 | 対策 | 実装 |
|------|------|------|
| **認証** | セッションベース認証 | Laravel Breeze |
| **パスワード** | bcryptハッシュ化 | Laravel標準 |
| **CSRF保護** | トークン検証 | Laravel標準 |
| **XSS対策** | エスケープ処理 | Blade `{{ }}` |
| **SQLインジェクション** | プリペアドステートメント | Eloquent ORM |
| **セッション管理** | セキュアクッキー | Laravel標準 |

## 9. パフォーマンス
### 9.1 最適化施策

Eloquentの遅延ロード回避（with()）
ページネーション（10件/ページ）
インデックス設定（entry_date, is_read等）

### 9.2 キャッシュ（本番のみ）

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```


## 10. モニタリング・ログ
### 10.1 ログ

場所: storage/logs/laravel.log
レベル: debug, info, warning, error

10.2 エラーハンドリング
```php
// app/Exceptions/Handler.php

public function register()
{
    $this->reportable(function (Throwable $e) {
        \Log::error($e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    });
}
```

## 11. テスト
### 11.1 テスト環境

ツール: PHPUnit（Laravel標準）
データベース: SQLite（in-memory）

### 11.2 テストコマンド
```bash
# 全テスト実行
php artisan test

# 特定のテスト
php artisan test --filter EntryTest
```




