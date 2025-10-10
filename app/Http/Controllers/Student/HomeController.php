<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * 生徒ホーム画面（連絡帳一覧）を表示
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 自分の連絡帳記録を取得（新しい順）
        $entries = $user->entries()
            ->orderBy('entry_date', 'desc')
            ->paginate(20);

        return view('student.home', compact('entries'));
    }
}
