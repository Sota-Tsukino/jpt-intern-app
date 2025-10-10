<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * 管理者ホーム画面（ユーザー一覧）を表示
     */
    public function index(Request $request): View
    {
        // 全ユーザーを取得（管理者を除く）
        $users = User::with('class')
            ->where('role', '!=', 'admin')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(50);

        return view('admin.home', compact('users'));
    }
}
