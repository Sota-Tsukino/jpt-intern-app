# データベース設計書

## 1. テーブル一覧

| テーブル名 | 論理名 | 説明 |
|----------|-------|------|
| users | ユーザー | 生徒、担任、管理者の情報 |
| classes | クラス | 学年・クラス情報 |
| entries | 連絡帳 | 生徒の連絡帳記録 |

---

## 2. ER図
```
users (ユーザー)
├─ id (PK)
├─ name
├─ email (UNIQUE)
├─ password
├─ role
├─ class_id (FK to classes, nullable)
└─ ...
classes (クラス)
├─ id (PK)
├─ grade
├─ class_name
└─ UNIQUE(grade, class_name)
entries (連絡帳)
├─ id (PK)
├─ user_id (FK to users)
├─ entry_date
├─ submitted_at
├─ health_status
├─ mental_status
├─ study_reflection
├─ club_reflection
├─ is_read
├─ read_at
├─ read_by (FK to users, nullable)
└─ UNIQUE(user_id, entry_date)
```

## 3. テーブル定義

### 3.1 users（ユーザー）

**概要**: 生徒、担任、管理者の情報を管理

| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | ユーザーID |
| name | VARCHAR(50) | NO | - | - | 氏名 |
| email | VARCHAR(255) | NO | - | UNIQUE | メールアドレス（ログインID） |
| email_verified_at | TIMESTAMP | YES | NULL | - | メール確認日時 |
| password | VARCHAR(255) | NO | - | - | パスワード（ハッシュ化） |
| role | ENUM('student','teacher','admin') | NO | 'student' | - | 役割 |
| class_id | BIGINT UNSIGNED | YES | NULL | FK | 所属クラスID（生徒・担任のみ） |
| remember_token | VARCHAR(100) | YES | NULL | - | ログイン保持トークン |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

**インデックス**:
- PRIMARY KEY: `id`
- UNIQUE: `email`
- INDEX: `role`
- INDEX: `class_id`

**外部キー**:
- `class_id` → `classes(id)` ON DELETE SET NULL

---

### 3.2 classes（クラス）

**概要**: 学年・クラス情報を管理

| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | クラスID |
| grade | TINYINT | NO | - | - | 学年（1〜3） |
| class_name | VARCHAR(10) | NO | - | - | クラス名（A, B, C） |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

**インデックス**:
- PRIMARY KEY: `id`
- UNIQUE: `(grade, class_name)`

**ビジネスルール**:
- 同じ学年に同じクラス名は存在しない

---

### 3.3 entries（連絡帳）

**概要**: 生徒の連絡帳記録を管理

| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | 記録ID |
| user_id | BIGINT UNSIGNED | NO | - | FK | 生徒ID |
| entry_date | DATE | NO | - | - | 記録対象日（前登校日） |
| submitted_at | TIMESTAMP | NO | - | - | 提出日時 |
| health_status | TINYINT | NO | - | CHECK(1-5) | 体調（1〜5） |
| mental_status | TINYINT | NO | - | CHECK(1-5) | メンタル（1〜5） |
| study_reflection | VARCHAR(500) | NO | - | - | 授業振り返り |
| club_reflection | VARCHAR(500) | YES | NULL | - | 部活振り返り |
| is_read | BOOLEAN | NO | FALSE | - | 既読フラグ |
| read_at | TIMESTAMP | YES | NULL | - | 既読日時 |
| read_by | BIGINT UNSIGNED | YES | NULL | FK | 既読処理した教師ID |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

**インデックス**:
- PRIMARY KEY: `id`
- UNIQUE: `(user_id, entry_date)`
- INDEX: `entry_date`
- INDEX: `is_read`

**外部キー**:
- `user_id` → `users(id)` ON DELETE CASCADE
- `read_by` → `users(id)` ON DELETE SET NULL

**ビジネスルール**:
- 同じ生徒が同じ記録対象日の記録を複数持たない

---

## 4. リレーション

### 4.1 users ← classes（多対一）
- 1つのクラスに複数のユーザー（生徒・担任）が所属
- 管理者は `class_id = NULL`

### 4.2 entries ← users（多対一）
- 1人の生徒が複数の連絡帳記録を持つ

### 4.3 entries ← users（既読者）（多対一）
- 1人の教師が複数の連絡帳を既読処理

---

## 5. マイグレーション実行順序

1. `create_classes_table`（最初）
2. `add_custom_fields_to_users_table`（Breeze標準を拡張）
3. `create_entries_table`（最後）

---

## 6. 初期データ（Seeder）

### 6.1 ClassSeeder
- 学年（1~3）
- クラス（A~B）
```
1年A組（id: 1, grade: 1, class_name: 'A'）
1年B組（id: 2, grade: 1, class_name: 'B'）
2年A組（id: 3, grade: 2, class_name: 'A'）
2年B組（id: 4, grade: 2, class_name: 'B'）
3年A組（id: 5, grade: 3, class_name: 'A'）
3年B組（id: 6, grade: 3, class_name: 'B'）
```

### 6.2 UserSeeder
- 管理者 × 1
- 担任 × 6（各クラス1名）
- 生徒 × 180（各クラス最大30名）
  
### 6.2 EntrySeeder
- 生徒1人のみ30件
- 上記以外の生徒は 1人3件


## 7. バックアップ・リストア

### バックアップ
```bash
mysqldump -u root -p laravel_notebook > backup.sql
リストア
mysql -u root -p laravel_notebook < backup.sql
