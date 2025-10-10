<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntryController extends Controller
{
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
        ]);

        // 更新
        $entry->update($validated);

        return redirect()
            ->route('student.entries.show', $entry)
            ->with('success', '連絡帳を更新しました。');
    }
}
