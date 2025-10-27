# データベース設計書

## 1. テーブル一覧

### 課題1（基本テーブル）
| テーブル名 | 論理名 | 説明 |
|----------|-------|------|
| users | ユーザー | 生徒、担任、管理者の情報 |
| classes | クラス | 学年・クラス情報 |
| notebooks | 連絡帳 | 生徒の連絡帳記録 |

### 課題2追加テーブル（提案のみ）
| テーブル名 | 論理名 | 説明 | 実装 |
|----------|-------|------|------|
| teacher_comments | 担任間共有メモ | 担任間での情報共有 | 📄 提案のみ |
| notifications | 通知 | システム内通知 | 📄 提案のみ |

---

## 2. ER図

### 課題1（基本構造）
```
users (ユーザー)
├─ id (PK)
├─ name
├─ email (UNIQUE)
├─ password
├─ role ENUM('student','teacher','admin')
├─ class_id (FK to classes, nullable)
└─ ...

classes (クラス)
├─ id (PK)
├─ grade
├─ class_name
└─ UNIQUE(grade, class_name)

notebooks (連絡帳)
├─ id (PK)
├─ user_id (FK to users)
├─ record_date
├─ submitted_at
├─ health_status
├─ mental_status
├─ study_reflection
├─ club_reflection
├─ is_read
├─ read_at
└─ UNIQUE(user_id, record_date)
```

### 課題2拡張（実装）
```
notebooks (連絡帳) ★拡張
├─ (課題1の全カラム)
│
├─ ★課題2追加カラム（実装）
├─ stamp_type ENUM('good','great','fighting','care')
├─ stamped_at TIMESTAMP
├─ teacher_feedback TEXT
├─ commented_at TIMESTAMP
├─ flag ENUM('none','watch','urgent')
├─ flagged_at TIMESTAMP
├─ flagged_by BIGINT (FK to users)
└─ flag_memo TEXT
```

### 課題2拡張（提案のみ）
```
teacher_comments (担任間共有メモ) ★新規テーブル
├─ id (PK)
├─ notebook_id (FK to notebooks)
├─ user_id (FK to users) ← 投稿者
├─ content TEXT
├─ priority ENUM('normal','important','urgent')
├─ created_at
└─ updated_at

notifications (通知) ★新規テーブル
├─ id (PK)
├─ user_id (FK to users) ← 通知先
├─ type ENUM('health_anomaly','mental_anomaly','submission_low','flag_urgent')
├─ title VARCHAR(255)
├─ message TEXT
├─ link_url VARCHAR(255)
├─ data JSON
├─ read_at TIMESTAMP
├─ created_at
└─ updated_at

users (ユーザー) ★ロール拡張（提案）
├─ role ENUM('student','teacher','grade_leader','assistant_teacher','admin')
└─ ...
```

---

## 3. テーブル定義

### 3.1 users（ユーザー）

**概要**: 生徒、担任、管理者の情報を管理

#### 課題1（基本構造）
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

#### 課題2提案：ロール拡張
```sql
-- roleカラムの変更（提案のみ）
role ENUM('student','teacher','grade_leader','assistant_teacher','admin') DEFAULT 'student'

-- grade_idカラムの追加（学年主任用）
grade_id BIGINT UNSIGNED NULL COMMENT '担当学年ID（学年主任のみ）'
```

**インデックス**:
- PRIMARY KEY: `id`
- UNIQUE: `email`
- INDEX: `role`
- INDEX: `class_id`

**外部キー**:
- `class_id` → `classes(id)` ON DELETE SET NULL

**ビジネスルール**:
- 管理者は `class_id = NULL`
- 生徒・担任は `class_id` 必須
- メールアドレスは一意

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

### 3.3 notebooks（連絡帳）

**概要**: 生徒の連絡帳記録を管理

#### 課題1（基本カラム）
| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | 記録ID |
| user_id | BIGINT UNSIGNED | NO | - | FK | 生徒ID |
| record_date | DATE | NO | - | - | 記録対象日（前登校日） |
| submitted_at | TIMESTAMP | NO | - | - | 提出日時 |
| health_status | TINYINT | NO | - | CHECK(1-5) | 体調（1〜5） |
| mental_status | TINYINT | NO | - | CHECK(1-5) | メンタル（1〜5） |
| study_reflection | VARCHAR(500) | NO | - | - | 授業振り返り |
| club_reflection | VARCHAR(500) | YES | NULL | - | 部活振り返り |
| is_read | BOOLEAN | NO | FALSE | - | 既読フラグ |
| read_at | TIMESTAMP | YES | NULL | - | 既読日時 |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

#### 課題2追加カラム（実装）
| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 | 実装 |
|---------|---------|------|----------|------|------|------|
| stamp_type | ENUM('good','great','fighting','care') | YES | NULL | - | スタンプ種類（既読処理時に必須） | ✅ 実装 |
| stamped_at | TIMESTAMP | YES | NULL | - | スタンプ日時 | ✅ 実装 |
| teacher_feedback | TEXT | YES | NULL | - | 生徒へのコメント | ✅ 実装 |
| commented_at | TIMESTAMP | YES | NULL | - | コメント日時 | ✅ 実装 |
| flag | ENUM('none','watch','urgent') | NO | 'none' | - | 注目フラグ | ✅ 実装 |
| flagged_at | TIMESTAMP | YES | NULL | - | フラグ設定日時 | ✅ 実装 |
| flagged_by | BIGINT UNSIGNED | YES | NULL | FK | フラグ設定者ID | ✅ 実装 |
| flag_memo | TEXT | YES | NULL | - | フラグメモ（気づきメモ） | ✅ 実装 |

**スタンプ種類の意味**:
- `good`: 👍 いいね
- `great`: ⭐ すごい
- `fighting`: 💪 がんばれ
- `care`: 💙 心配

**フラグ種類の意味**:
- `none`: フラグなし
- `watch`: ⚠️ 経過観察
- `urgent`: 🚨 要注意

**インデックス**:
- PRIMARY KEY: `id`
- UNIQUE: `(user_id, record_date)`
- INDEX: `record_date`
- INDEX: `is_read`
- INDEX: `flag` ★課題2追加
- INDEX: `flagged_at` ★課題2追加

**外部キー**:
- `user_id` → `users(id)` ON DELETE CASCADE
- `flagged_by` → `users(id)` ON DELETE SET NULL ★課題2追加

**ビジネスルール**:
- 同じ生徒が同じ記録対象日の記録を複数持たない
- 既読後は編集不可（アプリケーション制御）
- フラグは上書き保存（履歴は残さない）

---

### 3.4 teacher_comments（担任間共有メモ）★課題2提案のみ

**概要**: 担任間での情報共有メモ（スレッド形式）

| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | コメントID |
| notebook_id | BIGINT UNSIGNED | NO | - | FK | 連絡帳ID |
| user_id | BIGINT UNSIGNED | NO | - | FK | 投稿者ID（担任） |
| content | TEXT | NO | - | - | メモ内容 |
| priority | ENUM('normal','important','urgent') | NO | 'normal' | - | 重要度 |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

**インデックス**:
- PRIMARY KEY: `id`
- INDEX: `notebook_id`
- INDEX: `created_at`

**外部キー**:
- `notebook_id` → `notebooks(id)` ON DELETE CASCADE
- `user_id` → `users(id)` ON DELETE CASCADE

**ビジネスルール**:
- 同学年の担任・学年主任のみ閲覧・投稿可能
- 編集・削除は不可（履歴として保持）

---

### 3.5 notifications（通知）★課題2提案のみ

**概要**: システム内通知の管理（メール送信なし）

| カラム名 | データ型 | NULL | デフォルト | 制約 | 説明 |
|---------|---------|------|----------|------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | 通知ID |
| user_id | BIGINT UNSIGNED | NO | - | FK | 通知先ユーザーID（担任） |
| type | ENUM('health_anomaly','mental_anomaly','submission_low','flag_urgent') | NO | - | - | 通知タイプ |
| title | VARCHAR(255) | NO | - | - | 通知タイトル |
| message | TEXT | NO | - | - | 通知メッセージ |
| link_url | VARCHAR(255) | YES | NULL | - | リンク先URL（詳細画面） |
| data | JSON | YES | NULL | - | 追加データ（生徒ID等） |
| read_at | TIMESTAMP | YES | NULL | - | 既読日時 |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 作成日時 |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | - | 更新日時 |

**通知タイプの意味**:
- `health_anomaly`: 体調異常アラート（3日連続で体調2以下）
- `mental_anomaly`: メンタル異常アラート（3日連続でメンタル2以下）
- `submission_low`: 提出率低下アラート（クラス提出率60%以下）
- `flag_urgent`: 要注意フラグ設定通知

**インデックス**:
- PRIMARY KEY: `id`
- INDEX: `(user_id, read_at)`
- INDEX: `type`
- INDEX: `created_at`

**外部キー**:
- `user_id` → `users(id)` ON DELETE CASCADE

**ビジネスルール**:
- 通知は本人のみ閲覧可能
- 既読後も履歴として保持
- 自動生成（Laravelコマンド）

---

## 4. リレーション

### 課題1（基本リレーション）
```
users ← classes（多対一）
├─ 1つのクラスに複数のユーザー（生徒・担任）が所属
└─ 管理者は class_id = NULL

notebooks ← users（多対一）
└─ 1人の生徒が複数の連絡帳記録を持つ
```

### 課題2追加（実装）
```
notebooks → users（flagged_by）（多対一）
└─ 1人の教師が複数の連絡帳にフラグを設定
```

### 課題2追加（提案のみ）
```
teacher_comments ← notebooks（多対一）
├─ 1つの連絡帳に複数の担任コメント
└─ スレッド形式で複数の教師が投稿

teacher_comments ← users（多対一）
└─ 1人の教師が複数のコメントを投稿

notifications ← users（多対一）
└─ 1人のユーザーが複数の通知を受信
```

---

## 5. マイグレーション実行順序

### 課題1（基本構造）
```
1. create_classes_table
2. create_users_table（Breeze標準）
3. add_custom_fields_to_users_table（role, class_id追加）
4. create_notebooks_table
```

### 課題2（実装）
```
5. add_stamp_columns_to_notebooks_table
   ├─ stamp_type
   └─ stamped_at

6. add_feedback_columns_to_notebooks_table
   ├─ teacher_feedback
   └─ commented_at

7. add_flag_columns_to_notebooks_table
   ├─ flag
   ├─ flagged_at
   ├─ flagged_by
   └─ flag_memo
```

### 課題2（提案のみ）
```
8. create_teacher_comments_table
9. create_notifications_table
10. update_users_role_column（ロール拡張）
```

---

## 6. 初期データ（Seeder）

### 6.1 Seederの種類

本プロジェクトでは、用途に応じて2種類のSeederを用意します。

| Seeder種類 | 用途 | データ量 | 実行速度 |
|-----------|------|---------|---------|
| **開発用** | 日常的な開発・動作確認 | 少量（素早くテスト） | 高速（数秒） |
| **本番用/デモ用** | ページャー・検索機能の動作確認 | 大量（実運用想定） | 低速（数十秒〜1分） |

### 6.2 開発用Seeder（DevelopmentSeeder）

**目的**: 素早い動作確認、機能開発時のテスト

**データ量**:
```
- クラス: 6クラス（変更なし）
- 管理者: 1人
- 担任: 6人（各クラス1名ずつ）
- 生徒: 18人（各クラス3名）
- 連絡帳: 54件（各生徒3件ずつ）
```

**所要時間**: 約5秒

**実行コマンド**:
```bash
php artisan db:seed --class=DevelopmentSeeder
```

**データ構成**:
```php
// database/seeders/DevelopmentSeeder.php

// 1. ClassSeeder: 6クラス
$classes = [
    ['grade' => 1, 'class_name' => 'A'],
    ['grade' => 1, 'class_name' => 'B'],
    ['grade' => 2, 'class_name' => 'A'],
    ['grade' => 2, 'class_name' => 'B'],
    ['grade' => 3, 'class_name' => 'A'],
    ['grade' => 3, 'class_name' => 'B'],
];

// 2. UserSeeder（少量）
- 管理者 × 1人
- 担任 × 6人（各クラス1名）
- 生徒 × 18人（各クラス3名のみ）

// 3. NotebookSeeder（少量）
- 各生徒 × 3件（直近3日分のみ）
```

**ユーザー例**:
```
管理者: admin@example.com / password
担任: teacher1A@example.com / password
生徒: student1A01@example.com / password
```

---

### 6.3 本番用/デモ用Seeder（ProductionSeeder）

**目的**: ページャー動作確認、検索機能テスト、実運用想定のデモ

**データ量**:
```
- クラス: 6クラス（変更なし）
- 管理者: 1人
- 担任: 6人（各クラス1名ずつ）
- 生徒: 180人（各クラス30名）
- 連絡帳: 約550件
  - 生徒1人（デモ用）: 30件（推移グラフ確認用）
  - その他の生徒: 3件ずつ
```

**所要時間**: 約30秒〜1分

**実行コマンド**:
```bash
php artisan db:seed --class=ProductionSeeder
```

**データ構成**:
```php
// database/seeders/ProductionSeeder.php

// 1. ClassSeeder: 6クラス（開発用と同じ）
$classes = [
    ['grade' => 1, 'class_name' => 'A'],
    ['grade' => 1, 'class_name' => 'B'],
    ['grade' => 2, 'class_name' => 'A'],
    ['grade' => 2, 'class_name' => 'B'],
    ['grade' => 3, 'class_name' => 'A'],
    ['grade' => 3, 'class_name' => 'B'],
];

// 2. UserSeeder（大量）
- 管理者 × 1人
- 担任 × 6人（各クラス1名）
- 生徒 × 180人（各クラス30名）

// 3. NotebookSeeder（大量）
- 生徒1人（student1A01）: 30件（直近30日分、推移グラフ確認用）
- その他の生徒: 3件ずつ（合計約540件）
```

**管理者**:
```
氏名: 管理者
メール: admin@example.com
パスワード: password
ロール: admin
クラス: NULL
```

**担任例**:
```
氏名: 田中先生
メール: teacher1A@example.com
パスワード: password
ロール: teacher
クラス: 1年A組
```

**生徒例**:
```
氏名: 1年A組01番
メール: student1A01@example.com
パスワード: password
ロール: student
クラス: 1年A組
```

**連絡帳データ例**:
```php
[
    'user_id' => 3,  // 生徒ID（student1A01）
    'record_date' => '2025-10-20',
    'submitted_at' => '2025-10-21 08:30:00',
    'health_status' => 3,
    'mental_status' => 3,
    'study_reflection' => '今日は数学の授業で二次関数を学びました。グラフの書き方が難しかったです。',
    'club_reflection' => 'サッカー部でシュート練習をしました。',
    'is_read' => false,
    'read_at' => null,
]
```

---

### 6.4 Seederの実行方法

#### 開発時（通常）
```bash
# 開発用Seeder実行（推奨）
php artisan migrate:fresh && php artisan db:seed --class=DevelopmentSeeder

# または分けて実行
php artisan migrate:fresh
php artisan db:seed --class=DevelopmentSeeder
```

#### 本番デモ・動作確認時
```bash
# 本番用Seeder実行（推奨）
php artisan migrate:fresh && php artisan db:seed --class=ProductionSeeder

# または分けて実行
php artisan migrate:fresh
php artisan db:seed --class=ProductionSeeder
```

#### DatabaseSeederでの切り替え
```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    // 環境変数で切り替え
    $seederClass = config('app.env') === 'production'
        ? ProductionSeeder::class
        : DevelopmentSeeder::class;

    $this->call($seederClass);
}
```

これにより、`php artisan migrate:fresh --seed` で環境に応じた適切なSeederが実行されます。

---

## 7. SQL例

### 7.1 課題1（基本テーブル作成）

#### classesテーブル
```sql
CREATE TABLE classes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grade TINYINT NOT NULL COMMENT '学年（1〜3）',
    class_name VARCHAR(10) NOT NULL COMMENT 'クラス名（A, B, C）',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_grade_class (grade, class_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### usersテーブル（Breeze拡張）
```sql
ALTER TABLE users 
ADD COLUMN role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student' COMMENT '役割',
ADD COLUMN class_id BIGINT UNSIGNED NULL COMMENT '所属クラスID',
ADD INDEX idx_role (role),
ADD INDEX idx_class_id (class_id),
ADD CONSTRAINT fk_users_class_id FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL;
```

#### notebooksテーブル
```sql
CREATE TABLE notebooks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '生徒ID',
    record_date DATE NOT NULL COMMENT '記録対象日',
    submitted_at TIMESTAMP NOT NULL COMMENT '提出日時',
    health_status TINYINT NOT NULL COMMENT '体調（1〜5）',
    mental_status TINYINT NOT NULL COMMENT 'メンタル（1〜5）',
    study_reflection VARCHAR(500) NOT NULL COMMENT '授業振り返り',
    club_reflection VARCHAR(500) NULL COMMENT '部活振り返り',
    is_read BOOLEAN NOT NULL DEFAULT FALSE COMMENT '既読フラグ',
    read_at TIMESTAMP NULL COMMENT '既読日時',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_record_date (user_id, record_date),
    INDEX idx_record_date (record_date),
    INDEX idx_is_read (is_read),
    CONSTRAINT fk_notebooks_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    CHECK (health_status BETWEEN 1 AND 5),
    CHECK (mental_status BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 7.2 課題2（カラム追加 - 実装）
```sql
-- notebooksテーブルに課題2の機能を追加
ALTER TABLE notebooks
-- スタンプ機能（既読処理時に必須）
ADD COLUMN stamp_type ENUM('good', 'great', 'fighting', 'care') NULL COMMENT 'スタンプ種類（既読処理時に必須）' AFTER is_read,
ADD COLUMN stamped_at TIMESTAMP NULL COMMENT 'スタンプ日時' AFTER stamp_type,

-- 生徒へのコメント機能
ADD COLUMN teacher_feedback TEXT NULL COMMENT '生徒へのコメント' AFTER stamped_at,
ADD COLUMN commented_at TIMESTAMP NULL COMMENT 'コメント日時' AFTER teacher_feedback,

-- フラグ機能
ADD COLUMN flag ENUM('none', 'watch', 'urgent') NOT NULL DEFAULT 'none' COMMENT '注目フラグ' AFTER commented_at,
ADD COLUMN flagged_at TIMESTAMP NULL COMMENT 'フラグ設定日時' AFTER flag,
ADD COLUMN flagged_by BIGINT UNSIGNED NULL COMMENT 'フラグ設定者ID' AFTER flagged_at,
ADD COLUMN flag_memo TEXT NULL COMMENT 'フラグメモ' AFTER flagged_by,

-- インデックス追加
ADD INDEX idx_flag (flag),
ADD INDEX idx_flagged_at (flagged_at),

-- 外部キー追加
ADD CONSTRAINT fk_notebooks_flagged_by FOREIGN KEY (flagged_by) REFERENCES users(id) ON DELETE SET NULL;
```

---

### 7.3 課題2（新規テーブル作成 - 提案のみ）

#### teacher_commentsテーブル
```sql
CREATE TABLE teacher_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notebook_id BIGINT UNSIGNED NOT NULL COMMENT '連絡帳ID',
    user_id BIGINT UNSIGNED NOT NULL COMMENT '投稿者ID（担任）',
    content TEXT NOT NULL COMMENT 'メモ内容',
    priority ENUM('normal', 'important', 'urgent') NOT NULL DEFAULT 'normal' COMMENT '重要度',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_notebook_id (notebook_id),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_teacher_comments_notebook_id FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE CASCADE,
    CONSTRAINT fk_teacher_comments_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='担任間共有メモ';
```

#### notificationsテーブル
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '通知先ユーザーID',
    type ENUM('health_anomaly', 'mental_anomaly', 'submission_low', 'flag_urgent') NOT NULL COMMENT '通知タイプ',
    title VARCHAR(255) NOT NULL COMMENT '通知タイトル',
    message TEXT NOT NULL COMMENT '通知メッセージ',
    link_url VARCHAR(255) NULL COMMENT 'リンク先URL',
    data JSON NULL COMMENT '追加データ',
    read_at TIMESTAMP NULL COMMENT '既読日時',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_notifications_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知';
```

#### usersテーブル（ロール拡張）
```sql
-- roleカラムの変更
ALTER TABLE users 
MODIFY COLUMN role ENUM('student', 'teacher', 'grade_leader', 'assistant_teacher', 'admin') NOT NULL DEFAULT 'student' COMMENT '役割';

-- 学年主任用のgrade_idカラム追加
ALTER TABLE users 
ADD COLUMN grade_id BIGINT UNSIGNED NULL COMMENT '担当学年ID（学年主任のみ）' AFTER class_id,
ADD INDEX idx_grade_id (grade_id);
```

---

## 8. クエリ例

### 8.1 課題1（基本クエリ）

#### 担任の提出状況確認
```sql
-- 本日の提出状況
SELECT 
    u.id,
    u.name,
    n.record_date,
    n.health_status,
    n.mental_status,
    n.is_read,
    n.submitted_at
FROM users u
LEFT JOIN notebooks n ON u.id = n.user_id AND n.record_date = CURDATE() - INTERVAL 1 DAY
WHERE u.role = 'student' AND u.class_id = 1
ORDER BY u.id;
```

#### 未読の連絡帳一覧
```sql
SELECT 
    n.*,
    u.name as student_name
FROM notebooks n
JOIN users u ON n.user_id = u.id
WHERE n.is_read = FALSE 
  AND u.class_id = 1
ORDER BY n.submitted_at DESC;
```

---

### 8.2 課題2（実装クエリ）

#### フラグ付き生徒一覧
```sql
SELECT 
    u.id,
    u.name,
    n.record_date,
    n.health_status,
    n.mental_status,
    n.flag,
    n.flag_memo,
    n.flagged_at,
    flagged_user.name as flagged_by_name
FROM notebooks n
JOIN users u ON n.user_id = u.id
LEFT JOIN users flagged_user ON n.flagged_by = flagged_user.id
WHERE n.flag IN ('watch', 'urgent')
  AND u.class_id = 1
ORDER BY n.flag DESC, n.flagged_at DESC;
```

#### 個別生徒の推移データ
```sql
-- 直近30日間の体調・メンタル推移
SELECT 
    record_date,
    health_status,
    mental_status
FROM notebooks
WHERE user_id = 3
  AND record_date >= CURDATE() - INTERVAL 30 DAY
ORDER BY record_date ASC;
```

---

### 8.3 課題2（提案クエリ）

#### 体調異常検知
```sql
-- 3日連続で体調が2以下の生徒
SELECT 
    u.id,
    u.name,
    COUNT(*) as consecutive_days
FROM users u
JOIN notebooks n ON u.id = n.user_id
WHERE n.record_date >= CURDATE() - INTERVAL 3 DAY
  AND n.health_status <= 2
  AND u.role = 'student'
GROUP BY u.id, u.name
HAVING COUNT(*) = 3;
```

#### クラス全体の平均値
```sql
-- 直近1週間のクラス平均
SELECT 
    n.record_date,
    AVG(n.health_status) as avg_health,
    AVG(n.mental_status) as avg_mental,
    COUNT(*) as submission_count
FROM notebooks n
JOIN users u ON n.user_id = u.id
WHERE u.class_id = 1
  AND n.record_date >= CURDATE() - INTERVAL 7 DAY
GROUP BY n.record_date
ORDER BY n.record_date ASC;
```

---

## 9. バックアップ・リストア

### バックアップ
```bash
# 全データベースのバックアップ
mysqldump -u root -p laravel_notebook > backup_$(date +%Y%m%d).sql

# テーブル構造のみ
mysqldump -u root -p --no-data laravel_notebook > structure.sql

# 特定テーブルのみ
mysqldump -u root -p laravel_notebook notebooks users > notebooks_backup.sql
```

### リストア
```bash
# バックアップからリストア
mysql -u root -p laravel_notebook < backup_20251027.sql

# 特定テーブルのみリストア
mysql -u root -p laravel_notebook < notebooks_backup.sql
```

## 10. 課題1→課題2 変更サマリー
### 10.1 カラム追加（実装）
notebooksテーブルに8カラム追加
```sql
-- スタンプ機能（2カラム）
stamp_type ENUM('good','great','fighting','care') NULL
stamped_at TIMESTAMP NULL

-- コメント機能（2カラム）
teacher_feedback TEXT NULL
commented_at TIMESTAMP NULL

-- フラグ機能（4カラム）
flag ENUM('none','watch','urgent') DEFAULT 'none'
flagged_at TIMESTAMP NULL
flagged_by BIGINT UNSIGNED NULL
flag_memo TEXT NULL
```

### 10.2 テーブル追加（提案のみ）
```sql
-- 2つの新規テーブル
teacher_comments (担任間共有メモ)
notifications (通知)
```

### 10.3 カラム変更（提案のみ）
```sql
-- usersテーブル
role ENUM(...) ← 'grade_leader', 'assistant_teacher' 追加
grade_id BIGINT UNSIGNED NULL ← 新規追加
```

---

## 11. データ整合性チェック

### 課題1
```
✅ 同じ生徒が同じ記録対象日の記録を複数持たない
   → UNIQUE(user_id, record_date)

✅ 担任は必ずクラスに所属
   → アプリケーション層でバリデーション

✅ 体調・メンタルは1〜5の範囲
   → CHECK制約

✅ 既読後は編集不可
   → アプリケーション層で制御
```

### 課題2追加（実装）
```
✅ フラグ設定者は教師ロールのみ
   → Policy（権限管理クラス）で制御

✅ スタンプは4種類のみ
   → ENUM制約

✅ フラグは3種類のみ
   → ENUM制約

✅ フラグメモは1000文字以内
   → アプリケーション層でバリデーション
```

### 課題2追加（提案のみ）
```
✅ 担任間共有メモは同学年のみ閲覧
   → Policy で制御

✅ 通知は本人のみ閲覧
   → Controller で制御

✅ 学年主任は学年全体の連絡帳を閲覧可能
   → Policy で制御
```

## 12. パフォーマンス最適化
### 課題1
```sql
-- 基本インデックス
INDEX idx_record_date (record_date)        -- 日付検索
INDEX idx_is_read (is_read)                -- 既読/未読フィルタ
INDEX idx_role (role)                       -- ロール検索
INDEX idx_class_id (class_id)               -- クラス検索
```

### 課題2追加（実装）
```sql
-- 追加インデックス
INDEX idx_flag (flag)                       -- フラグ検索
INDEX idx_flagged_at (flagged_at)           -- フラグ日時ソート

```
### 課題2追加（提案のみ）
```sql
-- 通知用複合インデックス
INDEX idx_user_read (user_id, read_at)      -- 未読通知検索

-- 担任間共有メモ用
INDEX idx_notebook_id (notebook_id)         -- 連絡帳別メモ検索
INDEX idx_created_at (created_at)           -- 日時ソート
```

---

## 13. セキュリティ考慮事項

### 課題1
```
✅ パスワードのハッシュ化
   → Laravel標準（bcrypt）

✅ 外部キー制約による参照整合性
   → ON DELETE CASCADE / SET NULL

✅ 既読後は編集不可
   → アプリケーション層で制御

✅ CSRF保護
   → Laravel標準
```

### 課題2追加（実装）
```
✅ フラグメモは同学年の教師のみ閲覧
   → Policy（NotebookPolicy）で制御

✅ フラグ設定権限の制御
   → Middleware + Policy

✅ 生徒へのコメントは生徒本人のみ閲覧
   → Policy で制御
```

### 課題2追加（提案のみ）
```
✅ 担任間共有メモは同学年のみ閲覧
   → Policy（TeacherCommentPolicy）で制御

✅ 通知は本人のみ閲覧・既読操作
   → Controller で user_id チェック

✅ 学年主任の権限管理
   → Policy（NotebookPolicy）で grade_id チェック
```

---

## 14. データ保持・削除ポリシー

### 課題1
```
- 連絡帳データ: PoC期間中のみ保持
- ユーザーデータ: 卒業・退職時に削除
- クラスデータ: 年度替わりで更新
```

### 課題2追加（提案のみ）
```
- 通知データ: 3ヶ月後に自動削除（バッチ処理）
- 担任間共有メモ: 年度末まで保持
```

## 15. マイグレーションファイル例
### 課題1
#### 1. create_classes_table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('grade')->comment('学年（1〜3）');
            $table->string('class_name', 10)->comment('クラス名（A, B, C）');
            $table->timestamps();
            
            $table->unique(['grade', 'class_name'], 'unique_grade_class');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('classes');
    }
};
```
#### 2. add_custom_fields_to_users_table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'teacher', 'admin'])
                  ->default('student')
                  ->after('password')
                  ->comment('役割');
            
            $table->foreignId('class_id')
                  ->nullable()
                  ->after('role')
                  ->constrained()
                  ->onDelete('set null')
                  ->comment('所属クラスID');
            
            $table->index('role');
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'class_id']);
        });
    }
};
```
#### 3. create_notebooks_table


```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('生徒ID');
            
            $table->date('record_date')->comment('記録対象日');
            $table->timestamp('submitted_at')->comment('提出日時');
            
            $table->tinyInteger('health_status')->comment('体調（1〜5）');
            $table->tinyInteger('mental_status')->comment('メンタル（1〜5）');
            $table->string('study_reflection', 500)->comment('授業振り返り');
            $table->string('club_reflection', 500)->nullable()->comment('部活振り返り');
            
            $table->boolean('is_read')->default(false)->comment('既読フラグ');
            $table->timestamp('read_at')->nullable()->comment('既読日時');
            
            $table->timestamps();
            
            // ユニーク制約
            $table->unique(['user_id', 'record_date'], 'unique_user_record_date');
            
            // インデックス
            $table->index('record_date');
            $table->index('is_read');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('notebooks');
    }
};
```

### 課題2（実装）
#### 4. add_stamp_columns_to_notebooks_table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->enum('stamp_type', ['good', 'great', 'fighting', 'care'])
                  ->nullable()
                  ->after('is_read')
                  ->comment('スタンプ種類（既読処理時に必須）');

            $table->timestamp('stamped_at')
                  ->nullable()
                  ->after('stamp_type')
                  ->comment('スタンプ日時');
        });
    }

    public function down()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->dropColumn(['stamp_type', 'stamped_at']);
        });
    }
};
```

#### 5. add_feedback_columns_to_notebooks_table
   
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->text('teacher_feedback')
                  ->nullable()
                  ->after('stamped_at')
                  ->comment('生徒へのコメント');
            
            $table->timestamp('commented_at')
                  ->nullable()
                  ->after('teacher_feedback')
                  ->comment('コメント日時');
        });
    }
    
    public function down()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->dropColumn(['teacher_feedback', 'commented_at']);
        });
    }
};
```
#### 6. add_flag_columns_to_notebooks_table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->enum('flag', ['none', 'watch', 'urgent'])
                  ->default('none')
                  ->after('commented_at')
                  ->comment('注目フラグ');
            
            $table->timestamp('flagged_at')
                  ->nullable()
                  ->after('flag')
                  ->comment('フラグ設定日時');
            
            $table->foreignId('flagged_by')
                  ->nullable()
                  ->after('flagged_at')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('フラグ設定者ID');
            
            $table->text('flag_memo')
                  ->nullable()
                  ->after('flagged_by')
                  ->comment('フラグメモ（気づきメモ）');
            
            // インデックス追加
            $table->index('flag');
            $table->index('flagged_at');
        });
    }
    
    public function down()
    {
        Schema::table('notebooks', function (Blueprint $table) {
            $table->dropForeign(['flagged_by']);
            $table->dropIndex(['flag']);
            $table->dropIndex(['flagged_at']);
            $table->dropColumn(['flag', 'flagged_at', 'flagged_by', 'flag_memo']);
        });
    }
};
```
### 課題2（提案のみ）
#### 7. create_teacher_comments_table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_comments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('notebook_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('連絡帳ID');
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('投稿者ID（担任）');
            
            $table->text('content')->comment('メモ内容');
            
            $table->enum('priority', ['normal', 'important', 'urgent'])
                  ->default('normal')
                  ->comment('重要度');
            
            $table->timestamps();
            
            // インデックス
            $table->index('notebook_id');
            $table->index('created_at');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('teacher_comments');
    }
};
```
#### 8. create_notifications_table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('通知先ユーザーID');
            
            $table->enum('type', [
                'health_anomaly',
                'mental_anomaly',
                'submission_low',
                'flag_urgent'
            ])->comment('通知タイプ');
            
            $table->string('title')->comment('通知タイトル');
            $table->text('message')->comment('通知メッセージ');
            $table->string('link_url')->nullable()->comment('リンク先URL');
            $table->json('data')->nullable()->comment('追加データ');
            
            $table->timestamp('read_at')->nullable()->comment('既読日時');
            $table->timestamps();
            
            // インデックス
            $table->index(['user_id', 'read_at']);
            $table->index('type');
            $table->index('created_at');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
```
#### 9. update_users_role_column

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // roleカラムを一旦削除して再作成（ENUM拡張）
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'student',
                'teacher',
                'grade_leader',
                'assistant_teacher',
                'admin'
            ])->default('student')->after('password')->comment('役割');
            
            $table->index('role');
        });
        
        // grade_idカラム追加
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('grade_id')
                  ->nullable()
                  ->after('class_id')
                  ->comment('担当学年ID（学年主任のみ）');
            
            $table->index('grade_id');
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['grade_id']);
            $table->dropColumn('grade_id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'teacher', 'admin'])
                  ->default('student')
                  ->after('password')
                  ->comment('役割');
            
            $table->index('role');
        });
    }
};
```
---

## 16. 実装順序まとめ

### Phase 1: 課題1（基本機能）
```
1. classes テーブル作成
2. users テーブル拡張（role, class_id）
3. notebooks テーブル作成
4. Seeder でテストデータ作成
```

### Phase 2: 課題2（実装）
```
5. notebooks テーブルにスタンプカラム追加
6. notebooks テーブルにコメントカラム追加
7. notebooks テーブルにフラグカラム追加
8. 機能実装（Controller, View）
```

### Phase 3: 課題2（提案）
```
9. teacher_comments テーブル作成（提案資料のみ）
10. notifications テーブル作成（提案資料のみ）
11. users テーブルのロール拡張（提案資料のみ）
12. 提案資料作成（ER図、技術説明）
```

## 17. テスト用SQLクエリ
### データ確認
```sql
-- 全テーブルのレコード数確認
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'classes', COUNT(*) FROM classes
UNION ALL
SELECT 'notebooks', COUNT(*) FROM notebooks;

-- クラス別の生徒数
SELECT 
    c.grade,
    c.class_name,
    COUNT(u.id) as student_count
FROM classes c
LEFT JOIN users u ON c.id = u.class_id AND u.role = 'student'
GROUP BY c.id, c.grade, c.class_name
ORDER BY c.grade, c.class_name;

-- 提出状況サマリー
SELECT 
    DATE(n.record_date) as date,
    COUNT(DISTINCT n.user_id) as submitted_count,
    COUNT(DISTINCT CASE WHEN n.is_read = TRUE THEN n.user_id END) as read_count
FROM notebooks n
WHERE n.record_date >= CURDATE() - INTERVAL 7 DAY
GROUP BY DATE(n.record_date)
ORDER BY date DESC;
```
### 課題2データ確認


```sql
-- フラグ設定状況
SELECT 
    flag,
    COUNT(*) as count
FROM notebooks
WHERE flag != 'none'
GROUP BY flag;

-- スタンプ使用状況
SELECT
    stamp_type,
    COUNT(*) as count
FROM notebooks
WHERE stamp_type IS NOT NULL
GROUP BY stamp_type;
```

