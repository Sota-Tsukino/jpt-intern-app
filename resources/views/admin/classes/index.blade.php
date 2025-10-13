<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        学年・クラス管理
      </h2>
      <div class="text-sm text-gray-600">
        <span class="font-medium">今日:</span>
        {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- メッセージ -->
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

      <!-- クラス一覧 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">クラス一覧</h3>

          @if ($classes->count() > 0)
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">学年</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">クラス名</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">担任</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生徒数</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($classes as $class)
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $class->grade }}年
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $class->class_name }}組
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if ($class->teacher)
                          {{ $class->teacher->name }}
                        @else
                          <span class="text-gray-400">未配置</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $class->students->count() }}名
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-gray-500 text-center py-8">クラスが登録されていません。</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
