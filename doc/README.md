# 連絡帳管理システム ドキュメント

## 📚 ドキュメント一覧

### 1. [アプリケーションの利用マニュアル](Application_Manual.md)
アプリケーションの使い方を記載した利用マニュアルです。

**対象者**:
- 生徒
- 担任
- ユーザー管理者

**内容**:
- システム概要
- ログイン方法
- ロール別機能説明

---

### 2. [ER図](ER_Diagram.md)
データベース構造を記載したER図ドキュメントです。

**内容**:
- データベース構造図
- テーブル定義
- リレーション説明
- データ制約

---

### 3. [テストアカウント一覧](Test_Account.md)
検証・テスト用のアカウント情報です。

**内容**:
- 管理者アカウント
- 担任アカウント
- 生徒アカウント
- クラス一覧

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

## 📋 システム要件

- **PHP**: 8.2以上
- **Laravel**: 10.x
- **データベース**: MySQL 8.0以上
- **Node.js**: 18.x以上
- **ブラウザ**: Microsoft Edge 最新版（推奨）

---

## 🔒 セキュリティ

本番環境にデプロイする前に、以下を必ず実施してください：

1. ✅ 全アカウントのパスワード変更
2. ✅ `.env` ファイルの適切な設定
   - `APP_ENV=production`
   - `APP_DEBUG=false`
3. ✅ HTTPS通信の有効化
4. ✅ データベース認証情報の変更
5. ✅ テストアカウント情報の非公開化

---

## 📞 サポート

システムに関する問い合わせは、プロジェクト管理者までご連絡ください。

---

## 📄 ライセンス

このプロジェクトは選考目的のインターン課題として作成されました。
