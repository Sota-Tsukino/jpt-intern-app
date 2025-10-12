<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('連絡帳詳細') }}
      </h2>
      <div class="text-sm text-gray-600">
        <span class="font-medium">今日:</span>
        {{ \Carbon\Carbon::now()->format('Y年m月d日') }}（{{ ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] }}）
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
          <!-- 生徒情報 -->
          <div class="mb-6 pb-4 border-b">
            <h3 class="text-lg font-semibold mb-2">生徒情報</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <span class="font-medium">氏名:</span>
                <span class="ml-2">{{ $entry->user->name }}</span>
              </div>
              <div>
                <span class="font-medium">学年/クラス:</span>
                <span class="ml-2">{{ $entry->user->class->grade }}年{{ $entry->user->class->class_name }}組</span>
              </div>
            </div>
          </div>

          <!-- 連絡帳情報 -->
          <div class="mb-6 pb-4 border-b">
            <h3 class="text-lg font-semibold mb-2">連絡帳情報</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <span class="font-medium">記録対象日:</span>
                <span class="ml-2">{{ \Carbon\Carbon::parse($entry->entry_date)->format('Y年m月d日') }}</span>
              </div>
              <div>
                <span class="font-medium">提出日時:</span>
                <span class="ml-2">{{ \Carbon\Carbon::parse($entry->submitted_at)->format('Y年m月d日 H:i') }}</span>
              </div>
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

          <!-- 学習の振り返り -->
          <div class="mb-4">
            <label class="block font-medium text-gray-700 mb-2">学習の振り返り</label>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->study_reflection }}</p>
            </div>
          </div>

          <!-- 部活動の振り返り -->
          <div class="mb-6">
            <label class="block font-medium text-gray-700 mb-2">部活動の振り返り</label>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              @if ($entry->club_reflection)
                <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->club_reflection }}</p>
              @else
                <p class="text-gray-400 italic">記入なし</p>
              @endif
            </div>
          </div>

          <!-- 既読ステータス -->
          <div class="mb-6 pb-4 border-t pt-4">
            <div class="flex items-center gap-2 mb-2">
              <span class="font-medium text-gray-700">既読ステータス:</span>
              @if ($entry->is_read)
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-lg font-medium bg-green-100 text-green-800">
                    既読済み👍
                  </span>
                  <span class="text-2xl"></span>
                </div>
              @else
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-lg font-medium bg-gray-100 text-gray-800">
                  未読
                </span>
              @endif
            </div>
            @if ($entry->is_read && $entry->read_at)
              <div class="text-sm text-gray-600">
                <span class="font-medium">既読日時:</span>
                <span class="ml-2">{{ \Carbon\Carbon::parse($entry->read_at)->format('Y年m月d日 H:i') }}</span>
              </div>
            @endif
          </div>

          <!-- アクションボタン -->
          <div class="flex justify-between items-center">
            <a href="{{ route('teacher.home') }}"
              class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              一覧に戻る
            </a>

            @if (!$entry->is_read)
              <form method="POST" action="{{ route('teacher.entries.markAsRead', $entry) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  既読にする
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
