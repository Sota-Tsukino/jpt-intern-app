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
            return redirect()
                ->route('teacher.entries.show', $entry)
                ->with('error', 'この連絡帳は既に既読済みです。');
        }

        // 既読にする
        $entry->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
            'read_by' => $teacher->id,
        ]);

        return redirect()
            ->route('teacher.entries.show', $entry)
            ->with('success', '連絡帳を既読にしました。');
    }
}
