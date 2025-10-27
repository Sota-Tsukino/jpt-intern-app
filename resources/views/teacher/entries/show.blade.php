<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('連絡帳詳細') }}
        </h2>
        <div class="text-sm text-gray-600 mt-1">
          <span class="font-medium">今日:</span>
          {{ \Carbon\Carbon::now()->format('Y年m月d日') }}（{{ ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] }}）
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 成功/エラーメッセージ -->
      @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ session('success') }}</span>
        </div>
      @endif

      @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ session('error') }}</span>
        </div>
      @endif

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <!-- 基本情報 -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
            <!-- 生徒氏名 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">生徒氏名</label>
              <p class="text-lg text-gray-900">{{ $entry->user->name }}</p>
            </div>

            <!-- 所属学年・クラス -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">学年・クラス</label>
              <p class="text-lg text-gray-900">
                @if ($entry->user->class)
                  {{ $entry->user->class->grade }}年{{ $entry->user->class->class_name }}組
                @else
                  <span class="text-gray-400">未配置</span>
                @endif
              </p>
            </div>

            <!-- 記録対象日 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">記録対象日</label>
              <p class="text-lg text-gray-900">
                {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y年m月d日') }}
              </p>
            </div>

            <!-- 提出日時 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">提出日時</label>
              <p class="text-lg text-gray-900">
                {{ \Carbon\Carbon::parse($entry->submitted_at)->format('Y年m月d日 H:i') }}
              </p>
            </div>
          </div>

          <!-- 体調 -->
          <div class="mb-4">
            <label class="block font-medium text-gray-700 mb-2">体調</label>
            <div>
              <span
                class="text-lg font-semibold
                @if ($entry->health_status <= 2) text-red-600
                @elseif ($entry->health_status == 3) text-yellow-600
                @else text-green-600 @endif
              ">
                {{ $entry->health_status }}.{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->health_status] }}
              </span>
            </div>
          </div>

          <!-- メンタルステータス -->
          <div class="mb-6">
            <label class="block font-medium text-gray-700 mb-2">精神</label>
            <div>
              <span
                class="text-lg font-semibold
                @if ($entry->mental_status <= 2) text-red-600
                @elseif ($entry->mental_status == 3) text-yellow-600
                @else text-green-600 @endif
              ">
                {{ $entry->mental_status }}.{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->mental_status] }}
              </span>
            </div>
          </div>

          <!-- 授業振り返り -->
          <div class="mb-4">
            <label class="block font-medium text-gray-700 mb-2">授業振り返り</label>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->study_reflection }}</p>
            </div>
          </div>

          <!-- 部活振り返り -->
          <div class="mb-6">
            <label class="block font-medium text-gray-700 mb-2">部活振り返り</label>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              @if ($entry->club_reflection)
                <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->club_reflection }}</p>
              @else
                <p class="text-gray-400 italic">記入なし</p>
              @endif
            </div>
          </div>

          <!-- スタンプとコメント（課題2） -->
          <div class="mb-6 pb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">スタンプとコメント（生徒向け）</h3>

            @if ($entry->stamp_type)
              <!-- スタンプ保存済み -->
              <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-4">
                <div class="flex items-center mb-2">
                  <span class="text-3xl mr-3">
                    @if ($entry->stamp_type === 'good') 👍
                    @elseif ($entry->stamp_type === 'great') ⭐
                    @elseif ($entry->stamp_type === 'fighting') 💪
                    @elseif ($entry->stamp_type === 'care') 💙
                    @endif
                  </span>
                  <span class="text-blue-800 font-semibold">
                    @if ($entry->stamp_type === 'good') いいね
                    @elseif ($entry->stamp_type === 'great') すごい
                    @elseif ($entry->stamp_type === 'fighting') がんばれ
                    @elseif ($entry->stamp_type === 'care') 心配
                    @endif
                  </span>
                </div>
                @if ($entry->stamped_at)
                  <p class="text-sm text-blue-700">
                    スタンプ日時: {{ \Carbon\Carbon::parse($entry->stamped_at)->format('Y年m月d日 H:i') }}
                  </p>
                @endif
              </div>

              @if ($entry->teacher_feedback)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">先生からのコメント</label>
                  <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->teacher_feedback }}</p>
                  @if ($entry->commented_at)
                    <p class="text-xs text-gray-500 mt-2">
                      コメント日時: {{ \Carbon\Carbon::parse($entry->commented_at)->format('Y年m月d日 H:i') }}
                    </p>
                  @endif
                </div>
              @endif

              <!-- 既読ステータス -->
              <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center mb-2">
                  <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-green-800 font-semibold">既読</span>
                </div>
                @if ($entry->read_at)
                  <p class="text-sm text-green-700">
                    既読日時: {{ \Carbon\Carbon::parse($entry->read_at)->format('Y年m月d日 H:i') }}
                  </p>
                @endif
              </div>
            @else
              <!-- スタンプ未選択 - 入力フォーム -->
              <form method="POST" action="{{ route('teacher.entries.stamp', $entry) }}{{ request('from') ? '?from=' . request('from') : '' }}">
                @csrf
                @method('PATCH')

                <!-- スタンプ選択 -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-3">スタンプを選択 <span class="text-red-500">*</span></label>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="cursor-pointer">
                      <input type="radio" name="stamp_type" value="good" class="peer sr-only" required>
                      <div class="p-4 text-center border-2 border-gray-300 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                        <div class="text-4xl mb-2">👍</div>
                        <div class="text-sm font-medium">いいね</div>
                      </div>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" name="stamp_type" value="great" class="peer sr-only" required>
                      <div class="p-4 text-center border-2 border-gray-300 rounded-lg peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300 transition">
                        <div class="text-4xl mb-2">⭐</div>
                        <div class="text-sm font-medium">すごい</div>
                      </div>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" name="stamp_type" value="fighting" class="peer sr-only" required>
                      <div class="p-4 text-center border-2 border-gray-300 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition">
                        <div class="text-4xl mb-2">💪</div>
                        <div class="text-sm font-medium">がんばれ</div>
                      </div>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" name="stamp_type" value="care" class="peer sr-only" required>
                      <div class="p-4 text-center border-2 border-gray-300 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                        <div class="text-4xl mb-2">💙</div>
                        <div class="text-sm font-medium">心配</div>
                      </div>
                    </label>
                  </div>
                  @error('stamp_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- 生徒へのコメント -->
                <div class="mb-4">
                  <label for="teacher_feedback" class="block text-sm font-medium text-gray-700 mb-2">生徒へのコメント（任意）</label>
                  <textarea id="teacher_feedback" name="teacher_feedback" rows="4" maxlength="500"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="生徒へのメッセージを入力してください（500文字以内）">{{ old('teacher_feedback') }}</textarea>
                  <p class="mt-1 text-sm text-gray-500">
                    <span id="feedback-count">0</span> / 500文字
                  </p>
                  @error('teacher_feedback')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- 保存ボタン -->
                <div class="flex justify-end">
                  <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    スタンプを押す
                  </button>
                </div>
              </form>

              <script>
                // 文字数カウンター
                const textarea = document.getElementById('teacher_feedback');
                const counter = document.getElementById('feedback-count');
                if (textarea && counter) {
                  textarea.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                  });
                  // 初期値設定
                  counter.textContent = textarea.value.length;
                }
              </script>
            @endif
          </div>

          <!-- フラグ設定（課題2 - 教師間のみ） -->
          <div class="mb-6 pb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">注目レベル設定（教師間のみ）</h3>

            <!-- 現在のフラグ状態 -->
            @if ($entry->flag && $entry->flag !== 'none')
              <div class="mb-4 p-4 rounded-lg border-2
                @if ($entry->flag === 'watch') bg-yellow-50 border-yellow-300
                @elseif ($entry->flag === 'urgent') bg-red-50 border-red-300
                @endif
              ">
                <div class="flex items-center mb-2">
                  <span class="text-2xl mr-2">
                    @if ($entry->flag === 'watch') ⚠️
                    @elseif ($entry->flag === 'urgent') 🚨
                    @endif
                  </span>
                  <span class="font-semibold
                    @if ($entry->flag === 'watch') text-yellow-800
                    @elseif ($entry->flag === 'urgent') text-red-800
                    @endif
                  ">
                    @if ($entry->flag === 'watch') 経過観察
                    @elseif ($entry->flag === 'urgent') 要注意
                    @endif
                  </span>
                </div>
                @if ($entry->flagged_at)
                  <p class="text-sm
                    @if ($entry->flag === 'watch') text-yellow-700
                    @elseif ($entry->flag === 'urgent') text-red-700
                    @endif
                  ">
                    設定日時: {{ \Carbon\Carbon::parse($entry->flagged_at)->format('Y年m月d日 H:i') }}
                  </p>
                @endif
                @if ($entry->flag_memo)
                  <div class="mt-3 p-3 bg-white rounded border
                    @if ($entry->flag === 'watch') border-yellow-200
                    @elseif ($entry->flag === 'urgent') border-red-200
                    @endif
                  ">
                    <p class="text-sm font-medium text-gray-700 mb-1">気づきメモ:</p>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->flag_memo }}</p>
                  </div>
                @endif
              </div>
            @else
              <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-gray-600">フラグなし</span>
                </div>
              </div>
            @endif

            <!-- フラグ設定フォーム -->
            <form method="POST" action="{{ route('teacher.entries.updateFlag', $entry) }}{{ request('from') ? '?from=' . request('from') : '' }}">
              @csrf
              @method('PATCH')

              <!-- フラグ選択 -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">注目レベル <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                  <label class="cursor-pointer">
                    <input type="radio" name="flag" value="none" class="peer sr-only" {{ $entry->flag === 'none' || !$entry->flag ? 'checked' : '' }} required>
                    <div class="p-3 text-center border-2 border-gray-300 rounded-lg peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:border-gray-400 transition">
                      <div class="text-2xl mb-1">○</div>
                      <div class="text-sm font-medium">なし</div>
                    </div>
                  </label>
                  <label class="cursor-pointer">
                    <input type="radio" name="flag" value="watch" class="peer sr-only" {{ $entry->flag === 'watch' ? 'checked' : '' }} required>
                    <div class="p-3 text-center border-2 border-gray-300 rounded-lg peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300 transition">
                      <div class="text-2xl mb-1">⚠️</div>
                      <div class="text-sm font-medium">経過観察</div>
                    </div>
                  </label>
                  <label class="cursor-pointer">
                    <input type="radio" name="flag" value="urgent" class="peer sr-only" {{ $entry->flag === 'urgent' ? 'checked' : '' }} required>
                    <div class="p-3 text-center border-2 border-gray-300 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition">
                      <div class="text-2xl mb-1">🚨</div>
                      <div class="text-sm font-medium">要注意</div>
                    </div>
                  </label>
                </div>
                @error('flag')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- 気づきメモ -->
              <div class="mb-4">
                <label for="flag_memo" class="block text-sm font-medium text-gray-700 mb-2">気づきメモ（任意）</label>
                <textarea id="flag_memo" name="flag_memo" rows="4" maxlength="1000"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                  placeholder="この生徒について気づいたことをメモしてください（1000文字以内）">{{ old('flag_memo', $entry->flag_memo) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">
                  <span id="memo-count">{{ $entry->flag_memo ? mb_strlen($entry->flag_memo) : 0 }}</span> / 1000文字
                </p>
                @error('flag_memo')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- 保存ボタン -->
              <div class="flex justify-end">
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  フラグ設定を保存
                </button>
              </div>
            </form>

            <script>
              // 文字数カウンター（メモ）
              const memoTextarea = document.getElementById('flag_memo');
              const memoCounter = document.getElementById('memo-count');
              if (memoTextarea && memoCounter) {
                memoTextarea.addEventListener('input', function() {
                  memoCounter.textContent = this.value.length;
                });
              }
            </script>
          </div>

          <!-- アクションボタン -->
          <div class="flex justify-start items-center">
            @if (request('from') === 'past')
              <a href="{{ route('teacher.entries.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                過去記録一覧に戻る
              </a>
            @else
              <a href="{{ route('teacher.home') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ホームに戻る
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
