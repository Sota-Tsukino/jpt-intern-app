<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      連絡帳一覧
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      <!-- 本日の提出状況 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            本日の提出状況（{{ \Carbon\Carbon::parse($targetDate)->format('n月j日') }}（{{ ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($targetDate)->dayOfWeek] }}）分）
          </h3>
          @if ($todayEntry)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-semibold text-lg">提出済み</span>
              </div>
              <div class="ml-8 space-y-1">
                <p class="text-sm text-green-700">
                  提出日時: {{ \Carbon\Carbon::parse($todayEntry->submitted_at)->format('Y/m/d H:i') }}
                </p>
                @if ($todayEntry->is_read)
                  <p class="text-sm text-green-700">
                    👍 既読済み
                    @if ($todayEntry->read_at)
                      ({{ \Carbon\Carbon::parse($todayEntry->read_at)->format('m/d H:i') }})
                    @endif
                  </p>
                @else
                  <p class="text-sm text-gray-600">未読</p>
                @endif
              </div>
              <div class="mt-3">
                <a href="{{ route('student.entries.show', $todayEntry) }}"
                  class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  詳細を見る
                </a>
              </div>
            </div>
          @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <svg class="h-6 w-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-yellow-800 font-semibold text-lg">未提出</span>
              </div>
              <p class="ml-8 text-sm text-yellow-700 mb-3">本日の連絡帳をまだ提出していません。</p>
              <div class="mt-3">
                <a href="{{ route('student.entries.create') }}"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  連絡帳を作成する
                </a>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- 絞り込み・ソートフォーム -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">絞り込み検索</h3>
          <form method="GET" action="{{ route('student.home') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- 日付範囲 -->
              <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">
                  記録対象日（開始）
                </label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              </div>
              <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">
                  記録対象日（終了）
                </label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              </div>

              <!-- ソート順 -->
              <div>
                <label for="sort" class="block text-sm font-medium text-gray-700">並び順</label>
                <select name="sort" id="sort"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>新しい順</option>
                  <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>古い順</option>
                </select>
              </div>
            </div>

            <div class="flex gap-2">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                絞り込み
              </button>
              <a href="{{ route('student.home') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                クリア
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- 連絡帳一覧 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">連絡帳一覧</h3>

            <!-- 新規登録ボタン -->
            <div class="mb-6">
              <a href="{{ route('student.entries.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                新規登録
              </a>
            </div>

          </div>
          <!-- 表示件数 -->
          @if ($entries->total() > 0)
            <div class="mb-4 text-sm text-gray-600">
              全{{ $entries->total() }}件中 {{ $entries->firstItem() }}件〜{{ $entries->lastItem() }}件を表示
            </div>
          @endif

          @if ($entries->count() > 0)
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">記録対象日
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">提出日時</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">体調</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">メンタル</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">既読状況</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">操作</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($entries as $entry)
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y年m月d日') }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($entry->submitted_at)->format('Y/m/d H:i') }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-xs">
                        <span
                          class="font-semibold
                          @if ($entry->health_status <= 2) text-red-600
                          @elseif ($entry->health_status == 3) text-yellow-600
                          @else text-green-600 @endif
                        ">
                          {{ $entry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->health_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-xs">
                        <span
                          class="font-semibold
                          @if ($entry->mental_status <= 2) text-red-600
                          @elseif ($entry->mental_status == 3) text-yellow-600
                          @else text-green-600 @endif
                        ">
                          {{ $entry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->mental_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if ($entry->is_read)
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            👍既読
                          </span>
                        @else
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            未読
                          </span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('student.entries.show', $entry->id) }}"
                          class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                          詳細
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- ページネーション -->
            <div class="mt-6">
              {{ $entries->links() }}
            </div>
          @else
            <p class="text-gray-500 text-center py-8">まだ連絡帳が登録されていません。</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
