<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class EntryController extends Controller
{
    /**
     * 過去の連絡帳一覧を表示（今日の記録対象日以外）
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $teacher->load('class');

        // 今日の記録対象日（前登校日）を計算
        $today = Carbon::now();
        $previousSchoolDay = clone $today;
        do {
            $previousSchoolDay->subDay();
        } while ($previousSchoolDay->isWeekend());
        $targetDate = $previousSchoolDay->format('Y-m-d');

        // 担当クラスの生徒の連絡帳を取得（今日の記録対象日以外）
        $query = Entry::whereHas('user', function ($q) use ($teacher) {
            $q->where('class_id', $teacher->class_id);
        })->where('entry_date', '!=', $targetDate)->with(['user']);

        // 既読ステータスで絞り込み（デフォルトは既読済みのみ）
        $readStatus = $request->get('read_status', 'read'); // デフォルト: read（既読のみ）
        if ($readStatus === 'read') {
            $query->where('is_read', true);
        } elseif ($readStatus === 'unread') {
            $query->where('is_read', false);
        }
        // 'all' の場合は絞り込みなし

        // 生徒名で絞り込み
        if ($request->filled('student_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student_name . '%');
            });
        }

        // 記録対象日（開始日）で絞り込み
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        // 記録対象日（終了日）で絞り込み
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        // 記録対象日の降順でソート
        $entries = $query->orderBy('entry_date', 'desc')->paginate(20)->withQueryString();

        return view('teacher.entries.index', compact('entries', 'teacher'));
    }

    /**
     * 連絡帳の詳細を表示
     */
    public function show(Request $request, Entry $entry): View|RedirectResponse
    {
        $teacher = $request->user();

        // 生徒情報をロード
        $entry->load('user.class');

        // 担任のクラスの生徒かチェック
        if ($entry->user->class_id !== $teacher->class_id) {
            abort(403, 'この連絡帳を閲覧する権限がありません。');
        }

        return view('teacher.entries.show', compact('entry'));
    }

    /**
     * 連絡帳を既読にする
     */
    public function markAsRead(Request $request, Entry $entry): RedirectResponse
    {
        $teacher = $request->user();

        // 生徒情報をロード
        $entry->load('user');

        // 担任のクラスの生徒かチェック
        if ($entry->user->class_id !== $teacher->class_id) {
            abort(403, 'この連絡帳を既読にする権限がありません。');
        }

        // 既に既読済みかチェック
        if ($entry->is_read) {
            $redirectUrl = route('teacher.entries.show', $entry);
            if ($request->has('from')) {
                $redirectUrl .= '?from=' . $request->get('from');
            }
            return redirect($redirectUrl)
                ->with('error', 'この連絡帳は既に既読済みです。');
        }

        // 既読にする
        $entry->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
            'read_by' => $teacher->id,
        ]);

        $redirectUrl = route('teacher.entries.show', $entry);
        if ($request->has('from')) {
            $redirectUrl .= '?from=' . $request->get('from');
        }

        return redirect($redirectUrl)
            ->with('success', '連絡帳を既読にしました。');
    }
}
