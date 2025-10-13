<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      ユーザー詳細
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <!-- ユーザー基本情報 -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">基本情報</h3>

            <!-- 名前 -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">名前</label>
              <div class="text-base text-gray-900">{{ $user->name }}</div>
            </div>

            <!-- メールアドレス -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">メールアドレス</label>
              <div class="text-base text-gray-900">{{ $user->email }}</div>
            </div>

            <!-- ロール -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">ロール</label>
              <div class="text-base text-gray-900">
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
                @else
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    管理者
                  </span>
                @endif
              </div>
            </div>

            <!-- 学年・クラス -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">学年・クラス</label>
              <div class="text-base text-gray-900">
                @if ($user->class)
                  {{ $user->class->grade }}年{{ $user->class->class_name }}組
                @else
                  <span class="text-gray-500">未割当</span>
                @endif
              </div>
            </div>

            <!-- 登録日 -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">登録日</label>
              <div class="text-base text-gray-900">{{ $user->created_at->format('Y年m月d日 H:i') }}</div>
            </div>

            <!-- 最終更新日 -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">最終更新日</label>
              <div class="text-base text-gray-900">{{ $user->updated_at->format('Y年m月d日 H:i') }}</div>
            </div>
          </div>

          <!-- ボタン -->
          <div class="flex gap-2">
            <a href="{{ route('admin.home') }}"
              class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              ホームに戻る
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
