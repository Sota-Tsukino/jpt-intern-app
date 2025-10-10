# API設計書・画面遷移図

## 1. 画面遷移概要

### 1.1 ロール別画面数
| ロール | 画面数 | 備考 |
|--------|-------|------|
| 共通（認証） | 3画面 | Breeze標準 |
| 生徒 | 4画面 | 連絡帳の登録・閲覧 |
| 担任 | 5画面 | 提出状況確認・既読処理 |
| 管理者 | 7画面 | ユーザー・クラス管理 |
| **合計** | **19画面** | |

---

## 2. ルーティング設計

### 2.1 認証関連（Breeze標準）
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /login | login | ログイン画面 |
| POST | /login | - | ログイン処理 |
| POST | /logout | logout | ログアウト |
| GET | /forgot-password | password.request | パスワードリセット申請 |
| POST | /forgot-password | password.email | リセットメール送信 |
| GET | /reset-password/{token} | password.reset | パスワード再設定 |
| POST | /reset-password | password.update | パスワード更新 |

---

### 2.2 生徒機能
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /student/home | student.home | 生徒ホーム（連絡帳一覧） |
| GET | /student/entries/create | student.entries.create | 連絡帳登録画面 |
| POST | /student/entries | student.entries.store | 連絡帳登録処理 |
| GET | /student/entries/{id} | student.entries.show | 連絡帳詳細 |
| GET | /student/entries/{id}/edit | student.entries.edit | 連絡帳編集（未既読のみ） |
| PUT | /student/entries/{id} | student.entries.update | 連絡帳更新 |

---

### 2.3 担任機能
| メソッド | URI | 名前 | 説明 |
|---------|-----|------|------|
| GET | /teacher/home | teacher.home | 担任ホーム（提出状況） |
| GET | /teacher/entries/{id} | teacher.entries.show | 連絡帳詳細 |
| POST | /teacher/entries/{id}/read | teacher.entries.markAsRead | 既読処理 |
| GET | /teacher/entries | teacher.entries.index | 過去記録一覧 |

---

### 2.4 管理者機能
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

## 3. 画面遷移フロー

### 3.1 生徒の典型的なフロー
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
### 3.2 担任の典型的なフロー
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
[いいね！する] ボタン
↓
担任ホームに戻る（既読完了）
```
### 3.3 管理者の典型的なフロー
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

適用例:
// routes/web.php

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/home', [StudentHomeController::class, 'index'])->name('student.home');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/home', [TeacherHomeController::class, 'index'])->name('teacher.home');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin.home');
});
```

## 5. ログイン後のリダイレクト設定
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
## 6. バリデーションルール
### 6.1 連絡帳登録
```php
$request->validate([
    'health_status' => 'required|integer|between:1,5',
    'mental_status' => 'required|integer|between:1,5',
    'study_reflection' => 'required|string|max:500',
    'club_reflection' => 'nullable|string|max:500',
]);
```

### 6.2 ユーザー登録

```php
$request->validate([
    'name' => 'required|string|max:50',
    'email' => 'required|email|unique:users,email|max:255',
    'password' => 'required|string|min:8',
    'role' => 'required|in:student,teacher,admin',
    'class_id' => 'required_if:role,student,teacher|exists:classes,id',
]);
```

### 6.3 クラス作成
```php
$request->validate([
    'grade' => 'required|integer|between:1,3',
    'class_name' => 'required|string|max:10',
]);
```

## 7. レスポンスフォーマット
### 7.1 成功時
```php
return redirect()->route('student.home')
    ->with('success', '連絡帳を提出しました');
```
### 7.2 エラー時
```php
return back()->withErrors(['error' => 'エラーメッセージ'])
    ->withInput();
```

## 8. 画面遷移図（詳細）
画面遷移図の詳細は別途Figmaファイルを参照。

### 8.1 生徒画面

生徒ホーム
連絡帳登録
連絡帳詳細
絞り込み検索結果

### 8.2 担任画面

担任ホーム（提出状況サマリー）
連絡帳詳細
過去記録一覧
過去記録検索結果

### 8.3 管理者画面

管理者ホーム
ユーザー登録
ユーザー絞り込み検索結果
ユーザー詳細
ユーザー編集
学年・クラス管理
新規学年・クラス登録
