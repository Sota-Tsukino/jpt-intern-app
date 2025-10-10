<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * 担任ホーム画面（提出状況サマリー + 生徒一覧）を表示
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        // 担当クラスの生徒を取得
        $students = User::where('role', 'student')
            ->where('class_id', $teacher->class_id)
            ->orderBy('name')
            ->get();

        // 今日の日付
        $today = now()->format('Y-m-d');

        // 提出状況の集計
        $totalStudents = $students->count();
        $submittedCount = 0;
        $readCount = 0;

        foreach ($students as $student) {
            $todayEntry = $student->entries()
                ->where('entry_date', $today)
                ->first();

            if ($todayEntry) {
                $submittedCount++;
                if ($todayEntry->is_read) {
                    $readCount++;
                }
            }
        }

        $unsubmittedCount = $totalStudents - $submittedCount;

        return view('teacher.home', compact(
            'students',
            'totalStudents',
            'submittedCount',
            'readCount',
            'unsubmittedCount',
            'today'
        ));
    }
}
