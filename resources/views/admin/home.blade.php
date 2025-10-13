<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ユーザー管理
      </h2>
      <div class="text-sm text-gray-600">
        <span class="font-medium">今日:</span>
        {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- システム統計 -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- クラス数 -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">全クラス数</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $totalClasses }}クラス</p>
          </div>
        </div>

        <!-- 担任数 -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">全担任数</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $totalTeachers }}名</p>
          </div>
        </div>

        <!-- 生徒数 -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">全生徒数</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $totalStudents }}名</p>
          </div>
        </div>

      </div>

      <!-- 絞り込み検索 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">絞り込み検索</h3>
          <form method="GET" action="{{ route('admin.home') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <!-- ロール -->
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">ロール</label>
                <select name="role" id="role"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="">全て</option>
                  <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>生徒</option>
                  <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>担任</option>
                </select>
              </div>

              <!-- 名前 -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">名前</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="名前を入力">
              </div>

              <!-- クラス -->
              <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">クラス</label>
                <select name="class_id" id="class_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="">全て</option>
                  @foreach ($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                      {{ $class->grade }}年{{ $class->class_name }}組
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="flex gap-2">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                検索
              </button>
              <a href="{{ route('admin.home') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                リセット
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- ユーザー一覧 -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">ユーザー一覧</h3>
            <a href="{{ route('admin.classes.index') }}"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
              学年・クラス管理
            </a>
          </div>

          @if ($users->total() > 0)
            <div class="mb-4 text-sm text-gray-600">
              全{{ $users->total() }}件中 {{ $users->firstItem() }}件〜{{ $users->lastItem() }}件を表示
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">氏名</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メールアドレス
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ロール</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">学年・クラス
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">登録日</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($users as $user)
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $user->name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->email }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if ($user->role === 'student')
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            生徒
                          </span>
                        @elseif ($user->role === 'teacher')
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            担任
                          </span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if ($user->class)
                          {{ $user->class->grade }}年{{ $user->class->class_name }}組
                        @else
                          <span class="text-gray-400">未配置</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->created_at->format('Y/m/d') }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- ページネーション -->
            <div class="mt-4">
              {{ $users->links() }}
            </div>
          @else
            <p class="text-gray-500 text-center py-8">ユーザーが見つかりませんでした。</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
