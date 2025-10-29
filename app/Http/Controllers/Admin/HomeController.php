<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\ClassModel;

class HomeController extends Controller
{
    /**
     * 管理者ホーム画面（ユーザー一覧）を表示
     */
    public function index(Request $request): View
    {
        // 統計情報
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::whereIn('role', ['teacher', 'sub_teacher'])->count();
        $totalClasses = ClassModel::count();

        // ユーザー一覧を取得（管理者を除く）
        $query = User::with('class')->where('role', '!=', 'admin');

        // ロールで絞り込み
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 名前で絞り込み
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // クラスで絞り込み
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // ロール順、名前順でソート
        $users = $query->orderBy('role')->orderBy('name')->paginate(30)->withQueryString();

        // クラス一覧（絞り込み用）
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();

        return view('admin.home', compact('users', 'totalStudents', 'totalTeachers', 'totalClasses', 'classes'));
    }
}
