<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
