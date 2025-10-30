# 連絡帳管理システム ドキュメント

本ドキュメントは、JPカレッジインターンシップ課題として開発した「連絡帳管理システム PoC（Proof of Concept）」の技術資料です。

## 📚 ドキュメント一覧

### 1. [プレゼンテーション資料](Presentation.md) ⭐
システム全体の概要と実装内容をまとめたプレゼンテーション資料です。

**内容**:
- プロジェクト概要と成果物
- 機能要件と画面遷移図
- 課題１の工夫点・アピールポイント
- 課題２の提案と実装内容
- 技術スタックとセキュリティ対策
- 開発プロセスと感想

---

### 2. [アプリケーションの利用マニュアル](Application_Manual.md)
アプリケーションの使い方を記載した利用マニュアルです。

**対象者**:
- 生徒
- 担任
- 副担任
- ユーザー管理者

**内容**:
- システム概要
- ログイン方法
- ロール別機能説明

※課題１時点の仕様になります

---

### 3. [ER図](ER_Diagram.md)
データベース構造を記載したER図ドキュメントです。

**内容**:
- データベース構造図
- テーブル定義（users, classes, entries）
- リレーション説明
- データ制約
- 課題２での変更点まとめ

---

### 4. [テストアカウント一覧](Test_Account.md)
検証・テスト用のアカウント情報です。

**内容**:
- 管理者アカウント（1名）
- 担任アカウント（6名）
- 副担任アカウント（6名）※課題２で追加
- 生徒アカウント（180名）
- クラス一覧（6クラス）

---

## 🚀 クイックスタート

### 1. 環境構築

```bash
# 依存関係のインストール
composer install
npm install

# 環境変数の設定
cp .env.example .env
php artisan key:generate

# データベースのセットアップ
php artisan migrate
php artisan db:seed

# アセットのビルド
npm run build
```

### 2. ログイン

ブラウザで `http://localhost/login` にアクセスし、以下のアカウントでログイン:

| ロール | メールアドレス | パスワード |
|--------|---------------|-----------|
| 管理者 | admin@example.com | password |
| 担任 | teacher1A@example.com | password |
| 副担任 | subteacher1A@example.com | password |
| 生徒 | student1A01@example.com | password |

詳細は [テストアカウント一覧](Test_Account.md) を参照してください。

---

## 📋 システム情報

### システム規模
- **画面数**: 17画面（認証1 + 生徒4 + 担任5 + 管理者7）
- **ユーザー数**: 193名（管理者1 + 担任6 + 副担任6 + 生徒180）
- **クラス数**: 6クラス（1〜3年各2クラス）
- **デプロイURL**: https://jpt-intern-app.fitcloset.net

### システム要件
- **PHP**: 8.3
- **Laravel**: 10.x
- **データベース**: MySQL 8.4
- **Node.js**: 18.x以上
- **ブラウザ**: Microsoft Edge 最新版（推奨）

### 使用技術
- **フロントエンド**: HTML / CSS / Tailwind CSS / JavaScript / Chart.js
- **バックエンド**: PHP 8.3 / Laravel 10
- **認証**: Laravel Breeze
- **インフラ**: AWS (VPC, Route53, ELB, ACM, EC2, RDS, S3)
