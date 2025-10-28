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

        // 注意が必要な生徒を抽出（体調・メンタルが2以下）
        $alertStudents = $students->filter(function ($student) {
            if (!$student->todayEntry) {
                return false;
            }
            return $student->todayEntry->health_status <= 2 || $student->todayEntry->mental_status <= 2;
        });

        return view('teacher.home', compact(
            'teacher',
            'students',
            'totalStudents',
            'submittedCount',
            'readCount',
            'unsubmittedCount',
            'entryDate',
            'alertStudents'
        ));
    }

    /**
     * 生徒の体調・メンタル推移グラフを表示（課題2）
     */
    public function showGraph(Request $request, User $user): View
    {
        $teacher = $request->user();
        $teacher->load('class');

        // 指定された生徒が担当クラスの生徒かチェック
        if ($user->role !== 'student' || $user->class_id !== $teacher->class_id) {
            abort(403, 'この生徒の情報を閲覧する権限がありません。');
        }

        // 過去30日分の連絡帳データを取得（記録対象日の降順）
        $entries = $user->entries()
            ->where('entry_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('entry_date', 'asc')
            ->get();

        // グラフ用にデータを整形
        $dates = $entries->pluck('entry_date')->map(function ($date) {
            return Carbon::parse($date)->format('m/d');
        })->toArray();

        $healthData = $entries->pluck('health_status')->toArray();
        $mentalData = $entries->pluck('mental_status')->toArray();

        return view('teacher.students.graph', compact(
            'teacher',
            'user',
            'entries',
            'dates',
            'healthData',
            'mentalData'
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

    /**
     * クラス全体の統計グラフを表示（課題2）
     */
    public function showClassStatistics(Request $request): View
    {
        $teacher = $request->user();
        $teacher->load('class');

        // 日付パラメータを取得（デフォルトは過去30日）
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // クラス全体の連絡帳データを取得
        $entries = \App\Models\Entry::whereHas('user', function ($query) use ($teacher) {
            $query->where('role', 'student')
                  ->where('class_id', $teacher->class_id);
        })
        ->where('entry_date', '>=', $startDate->format('Y-m-d'))
        ->where('entry_date', '<=', $endDate->format('Y-m-d'))
        ->orderBy('entry_date', 'asc')
        ->get();

        // 日付ごとにデータを集計
        $dailyStats = $entries->groupBy('entry_date')->map(function ($dayEntries) {
            return [
                'health_avg' => round($dayEntries->avg('health_status'), 2),
                'mental_avg' => round($dayEntries->avg('mental_status'), 2),
                'count' => $dayEntries->count(),
            ];
        });

        // グラフ用にデータを整形
        $dates = $dailyStats->keys()->map(function ($date) {
            return Carbon::parse($date)->format('m/d');
        })->toArray();

        $healthData = $dailyStats->pluck('health_avg')->toArray();
        $mentalData = $dailyStats->pluck('mental_avg')->toArray();
        $submissionCounts = $dailyStats->pluck('count')->toArray();

        return view('teacher.class.statistics', compact(
            'teacher',
            'dates',
            'healthData',
            'mentalData',
            'submissionCounts',
            'startDate',
            'endDate'
        ));
    }
}
