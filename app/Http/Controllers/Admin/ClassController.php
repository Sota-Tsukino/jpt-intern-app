<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * クラス一覧を表示
     */
    public function index(): View
    {
        $classes = ClassModel::with(['teacher', 'students'])
            ->orderBy('grade')
            ->orderBy('class_name')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * クラス新規作成フォームを表示
     */
    public function create(): View
    {
        // 担任候補（クラス未配置の担任）
        $teachers = User::where('role', 'teacher')
            ->whereNull('class_id')
            ->orderBy('name')
            ->get();

        return view('admin.classes.create', compact('teachers'));
    }

    /**
     * クラスを作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'class_name' => 'required|string|max:10',
            'teacher_id' => 'nullable|exists:users,id',
        ], [
            'grade.required' => '学年は必須です。',
            'grade.min' => '学年は1以上を指定してください。',
            'grade.max' => '学年は3以下を指定してください。',
            'class_name.required' => 'クラス名は必須です。',
            'teacher_id.exists' => '指定された担任が存在しません。',
        ]);

        // 学年とクラス名の組み合わせで重複チェック（大文字小文字を区別）
        // データベースのcollation設定（utf8mb4_bin）により大文字小文字が区別される
        $exists = ClassModel::where('grade', $validated['grade'])
            ->where('class_name', $validated['class_name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['class_name' => 'この学年・クラス名の組み合わせは既に登録されています。']);
        }

        // クラスを作成
        $class = ClassModel::create([
            'grade' => $validated['grade'],
            'class_name' => $validated['class_name'],
        ]);

        // 担任を配置
        if (!empty($validated['teacher_id'])) {
            $teacher = User::find($validated['teacher_id']);
            $teacher->update(['class_id' => $class->id]);
        }

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'クラスを作成しました。');
    }

    /**
     * クラス編集フォームを表示
     */
    public function edit(ClassModel $class): View
    {
        $class->load('teacher');

        // 担任候補（クラス未配置の担任 + 現在の担任）
        $teachers = User::where('role', 'teacher')
            ->where(function ($query) use ($class) {
                $query->whereNull('class_id')
                    ->orWhere('class_id', $class->id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    /**
     * クラスを更新
     */
    public function update(Request $request, ClassModel $class): RedirectResponse
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:1|max:3',
            'class_name' => 'required|string|max:10',
            'teacher_id' => 'nullable|exists:users,id',
        ], [
            'grade.required' => '学年は必須です。',
            'grade.min' => '学年は1以上を指定してください。',
            'grade.max' => '学年は3以下を指定してください。',
            'class_name.required' => 'クラス名は必須です。',
            'teacher_id.exists' => '指定された担任が存在しません。',
        ]);

        
        // 学年とクラス名の組み合わせで重複チェック（大文字小文字を区別、現在のクラスは除外）
        // データベースのcollation設定（utf8mb4_bin）により大文字小文字が区別される
        $exists = ClassModel::where('grade', $validated['grade'])
            ->where('class_name', $validated['class_name'])
            ->where('id', '!=', $class->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['class_name' => 'この学年・クラス名の組み合わせは既に登録されています。']);
        }

        // 現在の担任をクリア
        if ($class->teacher) {
            $class->teacher->update(['class_id' => null]);
        }

        // クラス情報を更新
        $class->update([
            'grade' => $validated['grade'],
            'class_name' => $validated['class_name'],
        ]);

        // 新しい担任を配置
        if (!empty($validated['teacher_id'])) {
            $teacher = User::find($validated['teacher_id']);
            $teacher->update(['class_id' => $class->id]);
        }

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'クラスを更新しました。');
    }

    /**
     * クラスを削除
     */
    public function destroy(ClassModel $class): RedirectResponse
    {
        // 担任もしくは生徒が配置されている場合は削除不可
        if ($class->teacher || $class->students()->count() > 0) {
            return redirect()
                ->route('admin.classes.index')
                ->with('error', '担任もしくは生徒が配置されているクラスは削除できません。');
        }

        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'クラスを削除しました。');
    }
}
