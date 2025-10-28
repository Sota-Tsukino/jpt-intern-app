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
        // ※stamp_typeが存在する = 既読とみなす
        $readStatus = $request->get('read_status', 'read'); // デフォルト: read（既読のみ）
        if ($readStatus === 'read') {
            $query->whereNotNull('stamp_type');
        } elseif ($readStatus === 'unread') {
            $query->whereNull('stamp_type');
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
     * スタンプ・生徒へのコメントを保存（課題2）
     * ※スタンプ保存と同時に既読処理も実行
     */
    public function stamp(Request $request, Entry $entry): RedirectResponse
    {
        $teacher = $request->user();

        // 生徒情報をロード
        $entry->load('user');

        // 担任のクラスの生徒かチェック
        if ($entry->user->class_id !== $teacher->class_id) {
            abort(403, 'この連絡帳を編集する権限がありません。');
        }

        // バリデーション
        $validated = $request->validate([
            'stamp_type' => 'required|in:good,great,fighting,care',
            'teacher_feedback' => 'nullable|string|max:500',
        ], [
            'stamp_type.required' => 'スタンプは必須です。',
            'stamp_type.in' => '無効なスタンプが選択されています。',
            'teacher_feedback.max' => 'コメントは500文字以内で入力してください。',
        ]);

        // スタンプ・コメントを保存（スタンプ保存時点で既読扱いとする）
        $entry->update([
            'stamp_type' => $validated['stamp_type'],
            'stamped_at' => Carbon::now(),
            'stamped_by' => $teacher->id,
            'teacher_feedback' => $validated['teacher_feedback'],
            'commented_at' => $validated['teacher_feedback'] ? Carbon::now() : null,
        ]);

        $redirectUrl = route('teacher.entries.show', $entry);
        if ($request->has('from')) {
            $redirectUrl .= '?from=' . $request->get('from');
        }

        return redirect($redirectUrl)
            ->with('success', 'スタンプとコメントを保存しました。');
    }

    /**
     * フラグ・気づきメモを保存（課題2）
     */
    public function updateFlag(Request $request, Entry $entry): RedirectResponse
    {
        $teacher = $request->user();

        // 生徒情報をロード
        $entry->load('user');

        // 担任のクラスの生徒かチェック
        if ($entry->user->class_id !== $teacher->class_id) {
            abort(403, 'この連絡帳を編集する権限がありません。');
        }

        // バリデーション
        $validated = $request->validate([
            'flag' => 'required|in:none,watch,urgent',
            'flag_memo' => 'nullable|string|max:1000',
        ], [
            'flag.required' => '注目レベルは必須です。',
            'flag.in' => '無効な注目レベルが選択されています。',
            'flag_memo.max' => '気づきメモは1000文字以内で入力してください。',
        ]);

        // フラグ・気づきメモを保存
        $entry->update([
            'flag' => $validated['flag'],
            'flagged_at' => Carbon::now(),
            'flagged_by' => $teacher->id,
            'flag_memo' => $validated['flag_memo'],
        ]);

        $redirectUrl = route('teacher.entries.show', $entry);
        if ($request->has('from')) {
            $redirectUrl .= '?from=' . $request->get('from');
        }

        return redirect($redirectUrl)
            ->with('success', 'フラグ設定を保存しました。');
    }
}
