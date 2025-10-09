# Git運用ルール

## 1. ブランチ戦略（GitHub Flow）

### 1.1 基本方針
本プロジェクトでは**GitHub Flow**を採用します。

- `main` ブランチは常にデプロイ可能な状態を保つ
- `main` ブランチへ直接コミット・プッシュしない
- 機能・修正・ドキュメント単位でブランチを切って開発する
- 実装完了後、リモートにプッシュしてから `main` ブランチへマージ
- ローカルでの `main` ブランチへのマージは行わない

### 1.2 GitHub Flowの流れ

```
main (常にデプロイ可能)
↓
feature/xxx ブランチを作成
↓
実装・コミット・プッシュ
↓
GitLab上でマージリクエスト作成（オプション）
または
ローカルでリモートmainにマージ
↓
main にマージ
↓
デプロイ
```


### 1.3 ブランチ命名規則

| 種類 | プレフィックス | 例 |
|------|-------------|-----|
| 機能追加 | `feature/` | `feature/student-entry-create` |
| バグ修正 | `fix/` | `fix/entry-date-calculation` |
| ドキュメント更新 | `doc/` | `doc/requirements-update` |
| UI調整 | `ui/` | `ui/teacher-home-layout` |
| リファクタリング | `refactor/` | `refactor/entry-controller` |
| テスト追加 | `test/` | `test/entry-validation` |

### 1.4 ブランチの作成例
```bash
# 現在のブランチ確認
git branch

# 新しいブランチを作成して切り替え
git checkout -b feature/student-entry-create

# または（Git 2.23以降）
git switch -c feature/student-entry-create
```
## 2. コミット運用
### 2.1 コミット粒度

- 1作業項目（1機能・1修正）につき1コミット
- 作業が一区切りついたらコミットする
- 現在作業中のブランチでのみコミットする（mainへ直接はNG）
- こまめにコミットしたいので、1作業項目が完了するごとに確認を求める


## 2.2 良いコミット例
```bash
# マイグレーション作成
git add database/migrations/xxxx_create_entries_table.php
git commit -m "feat: 連絡帳テーブルのマイグレーション作成"

# コントローラー実装
git add app/Http/Controllers/Student/EntryController.php
git commit -m "feat: 生徒用連絡帳登録機能の実装"

# ビュー作成
git add resources/views/student/entries/create.blade.php
git commit -m "feat: 連絡帳登録画面のビュー作成"
```

### 2.3 悪いコミット例
```bash
# NG: 複数の機能を1コミットにまとめる
git add .
git commit -m "機能追加"

# NG: コミットメッセージが不明確
git commit -m "修正"

# NG: mainブランチに直接コミット
git checkout main
git commit -m "feat: ..."
```

## 3. コミットメッセージ規約
### 3.1 フォーマット
```
<type>: <subject>

[optional body]
```

### 3.2 Type一覧
| Type | 用途 | 例 |
|------|------|-----|
| **feat** | 新機能追加 | `feat: 連絡帳登録機能を実装` |
| **fix** | バグ修正 | `fix: 記録対象日の算出ロジックを修正` |
| **refactor** | リファクタリング | `refactor: EntryControllerを整理` |
| **style** | コードスタイル修正 | `style: インデント修正` |
| **doc** | ドキュメント更新 | `doc: 要件定義書を更新` |
| **test** | テスト追加・修正 | `test: 連絡帳登録機能のテスト追加` |
| **chore** | 設定・ビルド関連 | `chore: .gitignoreを更新` |
| **perf** | パフォーマンス改善 | `perf: クエリを最適化` |
| **revert** | コミット取り消し | `revert: feat: 連絡帳登録機能を実装` |

### 3.3 Subjectのルール

50文字以内
日本語OK
命令形で記述（例：「〜を追加」「〜を修正」）
末尾にピリオド不要
何を変更したかを明確に

### 3.4 良いコミットメッセージ例
```
✅ feat: 連絡帳登録機能を実装
✅ fix: 土日の記録対象日算出を修正
✅ doc: データベース設計書を追加
✅ refactor: 既読処理ロジックを関数化
✅ test: 連絡帳バリデーションのテスト追加
```

### 3.5 悪いコミットメッセージ例
```
❌ 修正
❌ update
❌ WIP
❌ 連絡帳登録機能を実装し、ビューも作成し、コントローラーも修正した
```

## 4. プッシュタイミング
### 4.1 基本ルール

- 各コミット後に即プッシュ
- リモートリポジトリに常に最新の状態を保つ
- ローカルの作業は常にバックアップされた状態にする

### 4.2 プッシュコマンド
```bash
# 通常のプッシュ
git push origin [ブランチ名]

# 例
git push origin feature/student-entry-create

# 初回プッシュ時（upstream設定）
git push -u origin feature/student-entry-create

# 以降は短縮可能
git push
```

## 5. マージ運用（GitHub Flow）
### 5.1 リモートmainブランチへのマージ（推奨）
重要：ローカルで main にマージせず、リモートで直接マージします。
```bash
# 1. 作業ブランチで実装完了後、リモートにプッシュ
git push origin feature/student-entry-create

# 2. リモート main に直接マージ（GitLab上で実行、またはコマンド）
# GitLabのWeb UIでマージ、または以下のコマンド

# ローカルのmainを最新化
git checkout main
git pull origin main

# リモート作業ブランチをmainにマージ（fast-forward）
git merge origin/feature/student-entry-create --ff-only

# mainをリモートにプッシュ
git push origin main

# 3. ローカルのmainを最新化
git checkout main
git pull origin main
```

### 5.2 GitLab Web UIでのマージ（より簡単・推奨）
```bash
# 1. 作業ブランチで実装完了後、リモートにプッシュ
git push origin feature/student-entry-create

# 2. GitLab Web UIでマージリクエスト作成（オプション）
# または、直接 main にマージ

# 3. マージ完了後、ローカルの main を更新
git checkout main
git pull origin main

# 4. 作業ブランチを削除
git branch -d feature/student-entry-create
```


### 5.3 マージ前の確認事項

 - 機能が正しく動作することを確認
 - テストが通ることを確認（あれば）
 - コンフリクトがないことを確認
 - コミットメッセージが適切
 - main ブランチが最新の状態

 
## 6. 作業フロー例（GitHub Flow）
### 6.1 新機能開発の流れ
```bash
# 1. mainブランチから最新を取得
git checkout main
git pull origin main

# 2. 作業ブランチを作成
git checkout -b feature/student-entry-create

# 3. 実装・コミット・プッシュ（こまめに）
# ファイル編集...
git add app/Http/Controllers/Student/EntryController.php
git commit -m "feat: 連絡帳登録コントローラーの実装"
git push origin feature/student-entry-create

# ファイル編集...
git add resources/views/student/entries/create.blade.php
git commit -m "feat: 連絡帳登録画面のビュー作成"
git push origin feature/student-entry-create

# 4. 機能完成後、GitLab Web UIで main にマージ
# または、以下のコマンドでリモートマージ

# ローカルのmainを最新化
git checkout main
git pull origin main

# リモート作業ブランチをマージ
git merge origin/feature/student-entry-create --ff-only
git push origin main

# 5. ローカルのmainを更新
git checkout main
git pull origin main

# 6. 作業ブランチを削除（オプション）
git branch -d feature/student-entry-create
git push origin --delete feature/student-entry-create
```

### 7. コンフリクト解決
## 7.1 コンフリクトが発生した場合

```bash
# mainの最新を取り込む
git checkout feature/student-entry-create
git pull origin main

# コンフリクト発生
# CONFLICT (content): Merge conflict in [ファイル名]

# コンフリクトファイルを確認
git status

# ファイルを開いて手動で解決
# <<<<<<<, =======, >>>>>>> の部分を編集

# 解決後、追加してコミット
git add [解決したファイル]
git commit -m "fix: コンフリクト解決"
git push origin feature/student-entry-create
```


## 8. よく使うGitコマンド
### 8.1 基本操作
```bash
# 現在の状態確認
git status

# 変更差分確認
git diff

# コミット履歴確認
git log --oneline

# ブランチ一覧
git branch

# リモートブランチ一覧
git branch -r

# 変更を取り消す（uncommitted）
git checkout -- [ファイル名]

# 直前のコミットを修正
git commit --amend

# コミットを取り消す
git revert [コミットID]

```


### 8.2 トラブルシューティング
```bash
# 間違ったブランチにコミットした場合
git log  # コミットIDを確認
git checkout [正しいブランチ]
git cherry-pick [コミットID]
git checkout [間違ったブランチ]
git reset --hard HEAD~1

# 未コミットの変更を一時退避
git stash
git stash pop

# リモートの最新を強制的に取得
git fetch origin
git reset --hard origin/main
```

## 9. .gitignore設定
```
# Laravel標準
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# IDE
.idea/
.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db
```

## 10. GitLabへのプッシュ確認
### 10.1 提出前の最終確認
```bash
# mainブランチに切り替え
git checkout main

# 最新の状態を確認
git pull origin main

# リモートの状態確認
git log origin/main --oneline

# /doc以下のファイル確認
ls -la doc/

# プッシュ
git push origin main
```

### 10.2 提出時の確認項目

 - mainブランチにすべての変更がマージされている
 - /doc以下にプレゼン資料がある
 - テストアカウント一覧が最新
 - README.mdが更新されている
 - .envファイルがコミットされていない
 - すべての作業ブランチがマージ済み
 
 