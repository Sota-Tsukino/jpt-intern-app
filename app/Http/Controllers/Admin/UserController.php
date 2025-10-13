<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * ユーザーの詳細を表示
     */
    public function show(User $user): View
    {
        // ユーザー情報とクラス情報をロード
        $user->load('class');

        return view('admin.users.show', compact('user'));
    }

    /**
     * ユーザー編集フォームを表示
     */
    public function edit(User $user): View
    {
        $user->load('class');

        // クラス一覧を取得
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();

        return view('admin.users.edit', compact('user', 'classes'));
    }

    /**
     * ユーザーを更新
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:student,teacher',
            'class_id' => 'nullable|exists:classes,id',
        ], [
            'name.required' => '名前は必須です。',
            'name.max' => '名前は255文字以内で入力してください。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '正しいメールアドレスの形式で入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'role.required' => 'ロールは必須です。',
            'role.in' => '正しいロールを選択してください。',
            'class_id.exists' => '指定されたクラスが存在しません。',
        ]);

        // ユーザー情報を更新
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'class_id' => $validated['class_id'],
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'ユーザー情報を更新しました。');
    }

    /**
     * パスワードをリセット
     */
    public function resetPassword(User $user): RedirectResponse
    {
        // ランダムな英数字8文字のパスワードを生成
        $newPassword = Str::random(8);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('new_password', $newPassword);
    }
}
