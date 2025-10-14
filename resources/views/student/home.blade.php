<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        連絡帳一覧
      </h2>
      <div class="text-sm text-gray-600">
        <span class="font-medium">今日:</span>
        {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 新規登録ボタン -->
      <div class="mb-6">
        <a href="{{ route('student.entries.create') }}"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
          新規登録
        </a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">記録対象日
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提出日時</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">体調</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メンタル</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">既読</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
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
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-semibold
                          @if ($entry->health_status <= 2) text-red-600
                          @elseif ($entry->health_status == 3) text-yellow-600
                          @else text-green-600
                          @endif
                        ">
                          {{ $entry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->health_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-semibold
                          @if ($entry->mental_status <= 2) text-red-600
                          @elseif ($entry->mental_status == 3) text-yellow-600
                          @else text-green-600
                          @endif
                        ">
                          {{ $entry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->mental_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if ($entry->is_read)
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            既読
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
