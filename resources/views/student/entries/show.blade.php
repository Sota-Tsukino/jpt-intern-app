<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          連絡帳詳細
        </h2>
        <div class="text-sm text-gray-600 mt-1">
          <span class="font-medium">今日:</span>
          {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <!-- 成功・エラーメッセージ -->
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
          <div class="mb-6 pb-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- 記録対象日 -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">記録対象日</label>
                <p class="text-lg font-semibold text-gray-900">
                  {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($entry->entry_date)->dayOfWeek] . '）') }}
                </p>
              </div>

              <!-- 提出日時 -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">提出日時</label>
                <p class="text-lg text-gray-900">
                  {{ \Carbon\Carbon::parse($entry->submitted_at)->format('Y/m/d H:i') }}
                </p>
              </div>

            </div>
          </div>

          <!-- 体調・メンタル -->
          <div class="mb-6 pb-6 border-b border-gray-200">
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

            <!-- メンタル -->
            <div>
              <label class="block font-medium text-gray-700 mb-2">メンタル</label>
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
          </div>

          <!-- 振り返り内容 -->
          <div class="mb-6 pb-6 border-b border-gray-200">
            <!-- 授業振り返り -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">授業振り返り</label>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->study_reflection }}</p>
              </div>
            </div>

            <!-- 部活振り返り -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">部活振り返り</label>
              <div class="bg-gray-50 rounded-lg p-4">
                @if ($entry->club_reflection)
                  <p class="text-gray-900 whitespace-pre-wrap">{{ $entry->club_reflection }}</p>
                @else
                  <p class="text-gray-400 italic">記入なし</p>
                @endif
              </div>
            </div>
          </div>

          <!-- 既読情報 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">既読状況</label>
            @if ($entry->is_read)
              <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center mb-2">
                  <span class="text-2xl mr-2">👍</span>
                  <span class="text-green-800 font-semibold">既読</span>
                </div>
                <p class="text-sm text-gray-700">
                  <span class="font-medium">既読日時:</span>
                  {{ \Carbon\Carbon::parse($entry->read_at)->format('Y/m/d H:i') }}
                </p>
                @if ($entry->reader)
                  <p class="text-sm text-gray-700">
                    <span class="font-medium">確認者:</span>
                    {{ $entry->reader->name }}
                  </p>
                @endif
              </div>
            @else
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd" />
                  </svg>
                  <span class="text-gray-600">未読（担任が確認中です）</span>
                </div>
              </div>
            @endif
          </div>
          <div class="flex gap-2 mt-6">
            @if (!$entry->is_read)
              <a href="{{ route('student.entries.edit', $entry) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                編集
              </a>
            @endif
            <a href="{{ route('student.home') }}"
              class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              一覧に戻る
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
