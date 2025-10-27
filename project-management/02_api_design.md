# API設計書・画面遷移図

## 1. 画面遷移概要

### 1.1 ロール別画面数

#### 課題1（基本機能）
| ロール | 画面数 | 備考 |
|--------|-------|------|
| 共通（認証） | 1画面 | ログインのみ（パスワードリセット機能は非表示） |
| 生徒 | 4画面 | 連絡帳の登録・閲覧 |
| 担任 | 3画面 | 提出状況確認・既読処理 |
| 管理者 | 7画面 | ユーザー・クラス管理 |
| **合計** | **15画面** | |

#### 課題2追加（実装する機能）
| ロール | 追加画面 | 備考 |
|--------|---------|------|
| 担任 | +2画面 | フラグ付き生徒一覧、個別生徒推移グラフ |
| **追加合計** | **+2画面** | 合計17画面 |

#### 課題2提案（提案のみの機能）
| ロール | 追加画面 | 備考 |
|--------|---------|------|
| 担任 | +3画面 | クラス統計グラフ、担任間共有メモ、通知一覧 |
| 学年主任 | +2画面 | 学年主任ダッシュボード、学年統計 |
| **提案合計** | **+5画面** | 将来実装時は合計22画面 |

---

## 2. ルーティング設計

### 2.1 認証関連（Breeze標準）
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /login | login | ログイン画面（パスワードリセットリンク非表示） |
| POST | /login | - | ログイン処理 |
| POST | /logout | logout | ログアウト |

---

### 2.2 生徒機能（課題1）
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /student/home | student.home | 生徒ホーム（連絡帳一覧） |
| GET | /student/notebooks/create | student.notebooks.create | 連絡帳登録画面 |
| POST | /student/notebooks | student.notebooks.store | 連絡帳登録処理 |
| GET | /student/notebooks/{id} | student.notebooks.show | 連絡帳詳細 |
| GET | /student/notebooks/{id}/edit | student.notebooks.edit | 連絡帳編集（未既読のみ） |
| PUT | /student/notebooks/{id} | student.notebooks.update | 連絡帳更新 |

---

### 2.3 担任機能

#### 課題1（基本機能）
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /teacher/home | teacher.home | 担任ホーム（提出状況サマリー + 本日の提出状況一覧） |
| GET | /teacher/notebooks/{id} | teacher.notebooks.show | 連絡帳詳細 |
| PATCH | /teacher/notebooks/{id}/mark-as-read | teacher.notebooks.markAsRead | 既読処理 |
| GET | /teacher/notebooks | teacher.notebooks.index | 過去記録一覧（絞り込み検索機能付き） |

#### 課題2追加機能（実装する）
| メソッド | URI | 名前 | 説明 | 実装 |
|---------|-----|------|------|------|
| PATCH | /teacher/notebooks/{id}/stamp | teacher.notebooks.stamp | スタンプ・生徒コメント保存 | ✅ 実装 |
| PATCH | /teacher/notebooks/{id}/flag | teacher.notebooks.updateFlag | フラグ設定・更新 | ✅ 実装 |
| GET | /teacher/notebooks/flagged | teacher.notebooks.flagged | フラグ付き生徒一覧 | ✅ 実装 |
| GET | /teacher/students/{id}/progress | teacher.students.progress | 個別生徒推移グラフ | ✅ 実装 |

#### 課題2提案機能（提案のみ）
| メソッド | URI | 名前 | 説明 | 実装 |
|---------|-----|------|------|------|
| GET | /teacher/class-statistics | teacher.classStatistics | クラス全体統計グラフ | 📄 提案のみ |
| POST | /teacher/notebooks/{id}/comments | teacher.comments.store | 担任間共有メモ投稿 | 📄 提案のみ |
| GET | /teacher/notifications | teacher.notifications.index | 通知一覧 | 📄 提案のみ |
| POST | /teacher/notifications/{id}/read | teacher.notifications.markAsRead | 通知を既読にする | 📄 提案のみ |
| POST | /teacher/notifications/read-all | teacher.notifications.markAllAsRead | すべて既読にする | 📄 提案のみ |

---

### 2.4 管理者機能（課題1）
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /admin/home | admin.home | 管理者ホーム（ユーザー一覧） |
| GET | /admin/users/create | admin.users.create | ユーザー登録画面 |
| POST | /admin/users | admin.users.store | ユーザー登録処理 |
| GET | /admin/users/{id} | admin.users.show | ユーザー詳細 |
| GET | /admin/users/{id}/edit | admin.users.edit | ユーザー編集画面 |
| PUT | /admin/users/{id} | admin.users.update | ユーザー更新 |
| POST | /admin/users/{id}/reset-password | admin.users.resetPassword | パスワードリセット |
| DELETE | /admin/users/{id} | admin.users.destroy | ユーザー削除 |
| GET | /admin/classes | admin.classes.index | クラス一覧 |
| GET | /admin/classes/create | admin.classes.create | クラス作成画面 |
| POST | /admin/classes | admin.classes.store | クラス作成処理 |
| DELETE | /admin/classes/{id} | admin.classes.destroy | クラス削除 |

---

### 2.5 学年主任機能（課題2 - 提案のみ）
| メソッド | URI | 名前 | 説明 | 実装 |
|---------|-----|------|------|------|
| GET | /grade-leader/dashboard | gradeLeader.dashboard | 学年主任ダッシュボード | 📄 提案のみ |
| GET | /grade-leader/statistics | gradeLeader.statistics | 学年全体統計グラフ | 📄 提案のみ |
| GET | /grade-leader/flagged | gradeLeader.flagged | 学年全体のフラグ付き生徒一覧 | 📄 提案のみ |

---

## 3. 画面遷移フロー

### 3.1 生徒の典型的なフロー（課題1）
```
ログイン
↓
生徒ホーム（連絡帳一覧）
↓
[連絡帳を作成] ボタン
↓
連絡帳登録画面
↓
体調・メンタル・振り返り入力
↓
[作成する] ボタン
↓
生徒ホームに戻る（成功メッセージ）
```

### 3.2 生徒の典型的なフロー（課題2 - スタンプ・コメント確認）
```
ログイン
↓
生徒ホーム（連絡帳一覧）
↓
既読済みの連絡帳に スタンプアイコン表示
↓
[詳細] ボタン
↓
連絡帳詳細画面
↓
スタンプ（👍⭐💪💙）表示
↓
先生からのコメント表示
```

---

### 3.3 担任の典型的なフロー（課題1）
```
ログイン
↓
担任ホーム（提出状況サマリー）
↓
[詳細] ボタン
↓
連絡帳詳細画面
↓
内容確認
↓
[既読にする] ボタン
↓
担任ホームに戻る（既読完了）
```

### 3.4 担任の典型的なフロー（課題2 - 実装：スタンプ・コメント・フラグ）
```
ログイン
↓
担任ホーム（提出状況サマリー）
↓
[詳細] ボタン
↓
連絡帳詳細画面
↓
━━━━━━━━━━━━━━━━━━
■ スタンプとコメント（生徒向け）
━━━━━━━━━━━━━━━━━━
↓
スタンプ選択（👍 いいね / ⭐ すごい / 💪 がんばれ / 💙 心配）
↓
生徒へのコメント入力（任意）
↓
[保存] ボタン
↓
━━━━━━━━━━━━━━━━━━
■ 注目レベル設定（教師間のみ）
━━━━━━━━━━━━━━━━━━
↓
フラグ選択（◯ なし / ● ⚠️ 経過観察 / ◯ 🚨 要注意）
↓
気づきメモ入力（任意）
↓
[変更を保存] ボタン
↓
担任ホームに戻る
```

### 3.5 担任の典型的なフロー（課題2 - 実装：フラグ付き生徒確認）
```
ログイン
↓
担任ホーム
↓
ナビゲーションメニューの [気になる生徒一覧] リンク
↓
フラグ付き生徒一覧画面
  - フラグ種類で絞り込み可能
  - 体調・メンタル・最新メモを表示
↓
[詳細] ボタン
↓
連絡帳詳細画面
↓
フラグ設定・メモ編集
```

### 3.6 担任の典型的なフロー（課題2 - 実装：推移グラフ確認）
```
ログイン
↓
担任ホーム
↓
生徒一覧の [📊 推移グラフ] ボタン
↓
個別生徒推移グラフ画面
  - 体調・メンタルの折れ線グラフ
  - 期間選択（1週間 / 1ヶ月 / 3ヶ月）
↓
グラフ確認
↓
[過去記録一覧へ] または [連絡帳詳細へ] リンク
```

### 3.7 担任の典型的なフロー（課題2 - 提案：クラス統計）
```
ログイン
↓
担任ホーム
↓
ナビゲーションメニューの [クラス全体統計グラフ] リンク
↓
クラス全体統計グラフ画面
  - クラス平均の推移（折れ線グラフ）
  - 本日の体調分布（棒グラフ）
  - 提出率の推移（折れ線グラフ）
  - 注目レベルの生徒数推移（積み上げ棒グラフ）
↓
期間選択（1週間 / 1ヶ月 / 3ヶ月）
```

---

### 3.8 管理者の典型的なフロー（課題1）
```
ログイン
↓
管理者ホーム（ユーザー一覧）
↓
[新規ユーザー作成] ボタン
↓
ユーザー登録画面
↓
氏名・メール・役割・クラス入力
↓
[登録] ボタン
↓
管理者ホームに戻る（初期パスワード表示）
```

---

## 4. Middleware構成

### 4.1 認証Middleware
- `auth`: ログイン必須
- `guest`: 未ログインのみアクセス可

### 4.2 ロール別Middleware（カスタム）
```php
// app/Http/Middleware/RoleMiddleware.php

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'アクセス権限がありません');
        }
        
        return $next($request);
    }
}
```

#### 適用例（課題1）
```php
// routes/web.php

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/home', [StudentHomeController::class, 'index'])
        ->name('student.home');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/home', [TeacherHomeController::class, 'index'])
        ->name('teacher.home');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/home', [AdminHomeController::class, 'index'])
        ->name('admin.home');
});
```

#### 課題2提案：学年主任対応
```php
// 学年主任は担任の権限も持つ
Route::middleware(['auth', 'role:teacher,grade_leader'])->group(function () {
    Route::get('/teacher/home', [TeacherHomeController::class, 'index'])
        ->name('teacher.home');
    
    // フラグ機能は担任・学年主任の両方がアクセス可能
    Route::get('/teacher/notebooks/flagged', [TeacherController::class, 'flagged'])
        ->name('teacher.notebooks.flagged');
});

// 学年主任専用
Route::middleware(['auth', 'role:grade_leader'])->group(function () {
    Route::get('/grade-leader/dashboard', [GradeLeaderController::class, 'dashboard'])
        ->name('gradeLeader.dashboard');
    
    Route::get('/grade-leader/statistics', [GradeLeaderController::class, 'statistics'])
        ->name('gradeLeader.statistics');
});
```

---

## 5. ログイン後のリダイレクト設定

### 課題1
```php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();
    
    $user = auth()->user();
    
    return match($user->role) {
        'student' => redirect()->intended('/student/home'),
        'teacher' => redirect()->intended('/teacher/home'),
        'admin' => redirect()->intended('/admin/home'),
        default => redirect()->intended('/dashboard'),
    };
}
```

### 課題2提案：学年主任対応
```php
public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();
    
    $user = auth()->user();
    
    return match($user->role) {
        'student' => redirect()->intended('/student/home'),
        'teacher' => redirect()->intended('/teacher/home'),
        'grade_leader' => redirect()->intended('/grade-leader/dashboard'),
        'assistant_teacher' => redirect()->intended('/teacher/home'),
        'admin' => redirect()->intended('/admin/home'),
        default => redirect()->intended('/dashboard'),
    };
}
```

---

## 6. バリデーションルール

### 6.1 連絡帳登録（課題1）
```php
$request->validate([
    'record_date' => 'required|date',
    'health_status' => 'required|integer|min:1|max:5',
    'mental_status' => 'required|integer|min:1|max:5',
    'study_reflection' => 'required|string|max:500',
    'club_reflection' => 'nullable|string|max:500',
], [
    'record_date.required' => '記録対象日は必須です。',
    'record_date.date' => '記録対象日は日付形式で入力してください。',
    'health_status.required' => '体調は必須です。',
    'health_status.integer' => '体調は整数で入力してください。',
    'health_status.min' => '体調は1以上を選択してください。',
    'health_status.max' => '体調は5以下を選択してください。',
    'mental_status.required' => 'メンタルは必須です。',
    'mental_status.integer' => 'メンタルは整数で入力してください。',
    'mental_status.min' => 'メンタルは1以上を選択してください。',
    'mental_status.max' => 'メンタルは5以下を選択してください。',
    'study_reflection.required' => '授業振り返りは必須です。',
    'study_reflection.max' => '授業振り返りは500文字以内で入力してください。',
    'club_reflection.max' => '部活振り返りは500文字以内で入力してください。',
]);
```

### 6.2 スタンプ・コメント保存（課題2 - 実装）
```php
$request->validate([
    'stamp_type' => 'required|in:good,great,fighting,care',
    'teacher_feedback' => 'nullable|string|max:500',
], [
    'stamp_type.required' => 'スタンプは必須です。',
    'stamp_type.in' => '無効なスタンプが選択されています。',
    'teacher_feedback.max' => 'コメントは500文字以内で入力してください。',
]);
```

### 6.3 フラグ設定（課題2 - 実装）
```php
$request->validate([
    'flag' => 'required|in:none,watch,urgent',
    'flag_memo' => 'nullable|string|max:1000',
], [
    'flag.required' => '注目レベルは必須です。',
    'flag.in' => '無効な注目レベルが選択されています。',
    'flag_memo.max' => '気づきメモは1000文字以内で入力してください。',
]);
```

### 6.4 ユーザー登録（課題1）
```php
$request->validate([
    'name' => 'required|string|max:50',
    'email' => 'required|email|unique:users,email|max:255',
    'password' => 'required|string|min:8',
    'role' => 'required|in:student,teacher,admin',
    'class_id' => 'required_if:role,student,teacher|exists:classes,id',
], [
    'name.required' => '氏名は必須です。',
    'name.max' => '氏名は50文字以内で入力してください。',
    'email.required' => 'メールアドレスは必須です。',
    'email.email' => 'メールアドレスの形式が正しくありません。',
    'email.unique' => 'このメールアドレスは既に登録されています。',
    'password.required' => 'パスワードは必須です。',
    'password.min' => 'パスワードは8文字以上で入力してください。',
    'role.required' => '役割は必須です。',
    'role.in' => '無効な役割が選択されています。',
    'class_id.required_if' => '生徒・担任の場合、クラスは必須です。',
    'class_id.exists' => '選択されたクラスが存在しません。',
]);
```

### 6.5 ユーザー登録（課題2提案：ロール拡張）
```php
$request->validate([
    'name' => 'required|string|max:50',
    'email' => 'required|email|unique:users,email|max:255',
    'password' => 'required|string|min:8',
    'role' => 'required|in:student,teacher,grade_leader,assistant_teacher,admin',
    'grade_id' => 'required_if:role,grade_leader|exists:grades,id',
    'class_id' => 'required_if:role,student,teacher,assistant_teacher|exists:classes,id',
]);
```

### 6.6 クラス作成（課題1）
```php
$request->validate([
    'grade' => 'required|integer|between:1,3',
    'class_name' => 'required|string|max:10',
], [
    'grade.required' => '学年は必須です。',
    'grade.between' => '学年は1〜3の範囲で入力してください。',
    'class_name.required' => 'クラス名は必須です。',
    'class_name.max' => 'クラス名は10文字以内で入力してください。',
]);
```

---

## 7. レスポンスフォーマット

### 7.1 成功時
```php
// リダイレクト + 成功メッセージ
return redirect()->route('student.home')
    ->with('success', '連絡帳を提出しました');

// JSON（課題2 - 通知既読処理など）
return response()->json([
    'success' => true,
    'message' => '通知を既読にしました'
]);
```

### 7.2 エラー時
```php
// バリデーションエラー
return back()->withErrors(['error' => 'エラーメッセージ'])
    ->withInput();

// JSON（課題2 - API）
return response()->json([
    'success' => false,
    'message' => 'エラーが発生しました'
], 422);
```

---

## 8. 画面遷移図（詳細）

### 8.1 生徒画面

#### 課題1
1. 生徒ホーム（連絡帳一覧）
2. 連絡帳登録
3. 連絡帳詳細
4. 絞り込み検索結果

#### 課題2追加
- 連絡帳詳細にスタンプ・コメント表示機能追加

---

### 8.2 担任画面

#### 課題1（基本機能）
1. 担任ホーム（提出状況サマリー + 本日の提出状況一覧）
2. 連絡帳詳細（既読処理ボタン）
3. 過去記録一覧（絞り込み検索機能付き）

#### 課題2 - 実装（追加画面）
4. **フラグ付き生徒一覧**
   - フラグ種類で絞り込み
   - 体調・メンタル・最新メモを表示

5. **個別生徒推移グラフ**
   - Chart.js 折れ線グラフ
   - 期間選択（1週間/1ヶ月/3ヶ月）

#### 課題2 - 実装（既存画面の拡張）
- 連絡帳詳細画面に以下を追加:
  - スタンプ選択UI
  - 生徒へのコメント入力欄
  - フラグ設定UI
  - 気づきメモ入力欄

#### 課題2 - 提案（追加画面）
6. **クラス全体統計グラフ**
   - クラス平均の推移
   - 本日の体調分布
   - 提出率の推移
   - 注目レベルの生徒数推移

7. **担任間共有メモ詳細**
   - タイムライン形式
   - スレッド表示
   - 重要度設定

8. **通知一覧**
   - 未読通知の表示
   - 既読管理
   - 通知タイプ別フィルタ

---

### 8.3 管理者画面（課題1）
1. 管理者ホーム（ユーザー一覧）
2. ユーザー登録
3. ユーザー絞り込み検索結果
4. ユーザー詳細
5. ユーザー編集
6. 学年・クラス管理（一覧表示・削除）
7. 新規学年・クラス登録

---

### 8.4 学年主任画面（課題2 - 提案）
1. 学年主任ダッシュボード
2. 学年全体統計グラフ
3. 学年全体のフラグ付き生徒一覧

---

## 9. 課題1→課題2 変更サマリー

### 追加ルート（実装）
```php
// スタンプ・コメント機能
PATCH /teacher/notebooks/{id}/stamp

// フラグ機能
PATCH /teacher/notebooks/{id}/flag
GET /teacher/notebooks/flagged

// 推移グラフ
GET /teacher/students/{id}/progress
```

### 追加ルート（提案のみ）
```php
// クラス統計
GET /teacher/class-statistics

// 担任間共有メモ
POST /teacher/notebooks/{id}/comments

// 通知機能
GET /teacher/notifications
POST /teacher/notifications/{id}/read
POST /teacher/notifications/read-all

// 学年主任機能
GET /grade-leader/dashboard
GET /grade-leader/statistics
GET /grade-leader/flagged
```

### 画面数の変化
- 課題1: 15画面
- 課題2実装後: 17画面（+2画面）
- 課題2全提案実装後: 22画面（+7画面）
