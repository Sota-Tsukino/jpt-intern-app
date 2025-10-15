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

        // 今日の日付
        $today = now();

        // 前登校日を計算（土日を除く前日）
        $previousSchoolDay = clone $today;
        do {
            $previousSchoolDay->subDay();
        } while ($previousSchoolDay->isWeekend());

        $targetDate = $previousSchoolDay->format('Y-m-d');

        // 本日提出すべき連絡帳（前登校日分）の提出状況を確認
        $todayEntry = $user->entries()->where('entry_date', $targetDate)->first();

        // クエリビルダーを開始
        $query = $user->entries();

        // 日付範囲の絞り込み
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        // ソート順
        $sort = $request->get('sort', 'desc');
        $query->orderBy('entry_date', $sort);

        // ページネーション
        $entries = $query->paginate(20)->withQueryString();

        return view('student.home', compact('entries', 'todayEntry', 'targetDate'));
    }
}
