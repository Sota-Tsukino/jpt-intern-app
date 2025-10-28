<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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

        // 担当クラス情報を取得
        $teacher->load('class');

        // 記録対象日（前登校日）を計算
        $entryDate = $this->calculateEntryDate();

        // 担当クラスの生徒を取得（記録対象日の連絡帳も一緒に取得）
        $students = User::where('role', 'student')
            ->where('class_id', $teacher->class_id)
            ->with(['entries' => function ($query) use ($entryDate) {
                $query->where('entry_date', $entryDate);
            }])
            ->orderBy('name')
            ->get();

        // 提出状況の集計
        $totalStudents = $students->count();
        $submittedCount = 0;
        $readCount = 0;

        foreach ($students as $student) {
            // 記録対象日の連絡帳を取得（既にeager loadingで取得済み）
            $todayEntry = $student->entries->first();

            // 生徒オブジェクトに記録対象日の連絡帳を追加
            $student->todayEntry = $todayEntry;

            if ($todayEntry) {
                $submittedCount++;
                if ($todayEntry->stamp_type) {
                    $readCount++;
                }
            }
        }

        $unsubmittedCount = $totalStudents - $submittedCount;

        return view('teacher.home', compact(
            'teacher',
            'students',
            'totalStudents',
            'submittedCount',
            'readCount',
            'unsubmittedCount',
            'entryDate'
        ));
    }

    /**
     * 記録対象日を計算（前登校日）
     */
    private function calculateEntryDate(): string
    {
        $today = Carbon::now();
        $entryDate = $today->copy()->subDay();

        // 土日をスキップ
        while ($entryDate->isWeekend()) {
            $entryDate->subDay();
        }

        return $entryDate->format('Y-m-d');
    }
}
