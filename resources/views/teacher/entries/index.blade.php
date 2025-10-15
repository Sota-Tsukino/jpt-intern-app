<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          過去記録一覧
        </h2>
        @if ($teacher->class)
          <div class="text-sm text-gray-600 mt-1">
            <span class="font-medium">担当:</span>
            {{ $teacher->class->grade }}年{{ $teacher->class->class_name }}組
          </div>
        @endif
      </div>
      <div class="text-sm text-gray-600">
        <span class="font-medium">今日:</span>
        {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 絞り込み検索 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">絞り込み検索</h3>
          <form method="GET" action="{{ route('teacher.entries.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
              <!-- 生徒名 -->
              <div>
                <label for="student_name" class="block text-sm font-medium text-gray-700 mb-1">生徒名</label>
                <input type="text" name="student_name" id="student_name" value="{{ request('student_name') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="生徒名を入力">
              </div>

              <!-- 記録対象日（開始） -->
              <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">記録対象日（開始）</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              </div>

              <!-- 記録対象日（終了） -->
              <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">記録対象日（終了）</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              </div>
            </div>

            <div class="flex gap-2">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                検索
              </button>
              <a href="{{ route('teacher.entries.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                リセット
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- 連絡帳一覧 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">過去記録一覧</h3>
            <a href="{{ route('teacher.home') }}"
              class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              ホームに戻る
            </a>
          </div>

          @if ($entries->total() > 0)
            <div class="mb-4 text-sm text-gray-600">
              全{{ $entries->total() }}件中 {{ $entries->firstItem() }}件〜{{ $entries->lastItem() }}件を表示
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">記録対象日
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生徒名</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提出日時
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">体調</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メンタル</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($entries as $entry)
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y/m/d') }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $entry->user->name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($entry->submitted_at)->format('Y/m/d H:i') }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span
                          class="font-semibold {{ $entry->health_status >= 4 ? 'text-green-600' : ($entry->health_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                          {{ $entry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->health_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span
                          class="font-semibold {{ $entry->mental_status >= 4 ? 'text-green-600' : ($entry->mental_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                          {{ $entry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->mental_status] }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('teacher.entries.show', ['entry' => $entry, 'from' => 'past']) }}"
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
            <div class="mt-4">
              {{ $entries->links() }}
            </div>
          @else
            <p class="text-gray-500 text-center py-8">連絡帳が見つかりませんでした。</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
