<x-app-layout>
  <x-slot name="header">
    <div>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        担任ホーム
      </h2>
      @if ($teacher->class)
        <div class="text-sm text-gray-600 mt-1">
          <span class="font-medium">担当:</span>
          {{ $teacher->class->grade }}年{{ $teacher->class->class_name }}組
        </div>
      @endif
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 提出状況サマリー -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            今日の提出状況（{{ \Carbon\Carbon::parse($entryDate)->format('n月j日') }}分）
          </h3>
          <div class="space-y-3">
            <!-- 提出済み -->
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-700 w-24">提出済み：</span>
              <span class="text-lg font-bold text-green-600">{{ $submittedCount }}名</span>
              <span class="text-sm text-gray-500 ml-2">/ {{ $totalStudents }}名</span>
            </div>

            <!-- 既読済み -->
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-700 w-24">既読済み：</span>
              <span class="text-lg font-bold text-purple-600">{{ $readCount }}名</span>
            </div>

            <!-- 未提出 -->
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-700 w-24">未提出：</span>
              <span class="text-lg font-bold text-red-600">{{ $unsubmittedCount }}名</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 注意が必要な生徒リスト -->
      @if ($alertStudents->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd" />
              </svg>
              注意が必要な生徒（{{ $alertStudents->count() }}名）
            </h3>
            <p class="text-sm text-yellow-700 mb-4">体調またはメンタルが「悪い」以下の生徒</p>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-yellow-100">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">氏名</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">体調</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">メンタル</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">操作</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-yellow-200">
                  @foreach ($alertStudents as $student)
                    <tr>
                      <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $student->name }}
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-xs">
                        <span
                          class="font-semibold {{ $student->todayEntry->health_status >= 4 ? 'text-green-600' : ($student->todayEntry->health_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                          {{ $student->todayEntry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$student->todayEntry->health_status] }}
                        </span>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-xs">
                        <span
                          class="font-semibold {{ $student->todayEntry->mental_status >= 4 ? 'text-green-600' : ($student->todayEntry->mental_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                          {{ $student->todayEntry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$student->todayEntry->mental_status] }}
                        </span>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('teacher.entries.show', ['entry' => $student->todayEntry, 'from' => 'home']) }}"
                          class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                          詳細
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif

      <!-- 生徒一覧 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">生徒一覧</h3>
            <a href="{{ route('teacher.entries.index') }}"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
              過去記録を見る
            </a>
          </div>

          @if ($students->count() > 0)
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">氏名</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">提出状況
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">既読状況
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">提出日時</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">体調</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">メンタル</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">操作</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($students as $student)
                    <tr class="{{ !$student->todayEntry ? 'bg-red-50' : '' }}">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $student->name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if ($student->todayEntry)
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            提出済み
                          </span>
                        @else
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            未提出
                          </span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if ($student->todayEntry)
                          @if ($student->todayEntry->stamp_type)
                            <span
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                              @if ($student->todayEntry->stamp_type === 'good') 👍
                              @elseif ($student->todayEntry->stamp_type === 'great') ⭐
                              @elseif ($student->todayEntry->stamp_type === 'fighting') 💪
                              @elseif ($student->todayEntry->stamp_type === 'care') 💙
                              @endif
                              既読
                            </span>
                          @else
                            <span
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                              未読
                            </span>
                          @endif
                        @else
                          <span class="text-gray-400">-</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if ($student->todayEntry)
                          {{ \Carbon\Carbon::parse($student->todayEntry->submitted_at)->format('Y/m/d H:i') }}
                        @else
                          <span class="text-gray-400">-</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                        @if ($student->todayEntry)
                          <span
                            class="font-semibold {{ $student->todayEntry->health_status >= 4 ? 'text-green-600' : ($student->todayEntry->health_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $student->todayEntry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$student->todayEntry->health_status] }}
                          </span>
                        @else
                          <span class="text-gray-400">-</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                        @if ($student->todayEntry)
                          <span
                            class="font-semibold {{ $student->todayEntry->mental_status >= 4 ? 'text-green-600' : ($student->todayEntry->mental_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $student->todayEntry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$student->todayEntry->mental_status] }}
                          </span>
                        @else
                          <span class="text-gray-400">-</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-2">
                          @if ($student->todayEntry)
                            <a href="{{ route('teacher.entries.show', ['entry' => $student->todayEntry, 'from' => 'home']) }}"
                              class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                              詳細
                            </a>
                          @endif
                          <a href="{{ route('teacher.students.graph', $student) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            推移グラフ
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-gray-500 text-center py-8">担当クラスに生徒が登録されていません。</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
