<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntryController extends Controller
{
    /**
     * 連絡帳新規登録画面を表示
     */
    public function create(Request $request): View
    {
        // 記録対象日を計算（前登校日）をデフォルトとして設定
        $defaultEntryDate = $this->calculateEntryDate();

        return view('student.entries.create', compact('defaultEntryDate'));
    }

    /**
     * 連絡帳を新規登録
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // バリデーション
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'health_status' => 'required|integer|min:1|max:5',
            'mental_status' => 'required|integer|min:1|max:5',
            'study_reflection' => 'required|string|max:500',
            'club_reflection' => 'nullable|string|max:500',
        ], [
            'entry_date.required' => '記録対象日は必須です。',
            'entry_date.date' => '記録対象日は日付形式で入力してください。',
            'health_status.required' => '体調は必須です。',
            'health_status.integer' => '体調は整数で入力してください。',
            'health_status.min' => '体調は1以上を選択してください。',
            'health_status.max' => '体調は5以下を選択してください。',
            'mental_status.required' => 'メンタルは必須です。',
            'mental_status.integer' => 'メンタルは整数で入力してください。',
            'mental_status.min' => 'メンタルは1以上を選択してください。',
            'mental_status.max' => 'メンタルは5以下を選択してください。',
            'study_reflection.required' => '授業振り返りは必須です。',
            'study_reflection.max' => '授業振り返りは500文字以内で入力してください。',
            'club_reflection.max' => '部活振り返りは500文字以内で入力してください。',
        ]);

        // 同じ記録対象日の連絡帳が既に存在するかチェック
        $existingEntry = Entry::where('user_id', $user->id)
            ->where('entry_date', $validated['entry_date'])
            ->first();

        if ($existingEntry) {
            return redirect()
                ->route('student.entries.show', $existingEntry)
                ->with('error', 'この記録対象日の連絡帳は既に登録されています。');
        }

        // 新規作成
        $entry = Entry::create([
            'user_id' => $user->id,
            'entry_date' => $validated['entry_date'],
            'submitted_at' => now(),
            'health_status' => $validated['health_status'],
            'mental_status' => $validated['mental_status'],
            'study_reflection' => $validated['study_reflection'],
            'club_reflection' => $validated['club_reflection'],
            'is_read' => false,
        ]);

        return redirect()
            ->route('student.entries.show', $entry)
            ->with('success', '連絡帳を登録しました。');
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
     * 連絡帳詳細画面を表示
     */
    public function show(Request $request, Entry $entry): View
    {
        $user = $request->user();

        // 自分の連絡帳かチェック
        if ($entry->user_id !== $user->id) {
            abort(403, 'この連絡帳を閲覧する権限がありません。');
        }

        // 既読者情報を取得
        $entry->load('reader');

        return view('student.entries.show', compact('entry'));
    }

    /**
     * 連絡帳編集画面を表示
     */
    public function edit(Request $request, Entry $entry): View|RedirectResponse
    {
        $user = $request->user();

        // 自分の連絡帳かチェック
        if ($entry->user_id !== $user->id) {
            abort(403, 'この連絡帳を編集する権限がありません。');
        }

        // 既読済みの場合は編集不可
        if ($entry->is_read) {
            return redirect()
                ->route('student.entries.show', $entry)
                ->with('error', '既読済みの連絡帳は編集できません。');
        }

        return view('student.entries.edit', compact('entry'));
    }

    /**
     * 連絡帳を更新
     */
    public function update(Request $request, Entry $entry): RedirectResponse
    {
        $user = $request->user();

        // 自分の連絡帳かチェック
        if ($entry->user_id !== $user->id) {
            abort(403, 'この連絡帳を編集する権限がありません。');
        }

        // 既読済みの場合は更新不可
        if ($entry->is_read) {
            return redirect()
                ->route('student.entries.show', $entry)
                ->with('error', '既読済みの連絡帳は編集できません。');
        }

        // バリデーション
        $validated = $request->validate([
            'health_status' => 'required|integer|min:1|max:5',
            'mental_status' => 'required|integer|min:1|max:5',
            'study_reflection' => 'required|string|max:500',
            'club_reflection' => 'nullable|string|max:500',
        ], [
            'health_status.required' => '体調は必須です。',
            'health_status.integer' => '体調は整数で入力してください。',
            'health_status.min' => '体調は1以上を選択してください。',
            'health_status.max' => '体調は5以下を選択してください。',
            'mental_status.required' => 'メンタルは必須です。',
            'mental_status.integer' => 'メンタルは整数で入力してください。',
            'mental_status.min' => 'メンタルは1以上を選択してください。',
            'mental_status.max' => 'メンタルは5以下を選択してください。',
            'study_reflection.required' => '授業振り返りは必須です。',
            'study_reflection.max' => '授業振り返りは500文字以内で入力してください。',
            'club_reflection.max' => '部活振り返りは500文字以内で入力してください。',
        ]);

        // 更新
        $entry->update($validated);

        return redirect()
            ->route('student.entries.show', $entry)
            ->with('success', '連絡帳を更新しました。');
    }
}
