<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      ユーザー詳細
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <!-- 新しいパスワード表示（一度だけ） -->
      @if (session('new_password'))
        <div class="mb-4 bg-yellow-50 border-2 border-yellow-400 px-6 py-4 rounded-lg" role="alert">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="ml-3 flex-1">
              @if (session('new_user_created'))
                <h3 class="text-lg font-bold text-yellow-800 mb-2">ユーザーを登録しました</h3>
              @else
                <h3 class="text-lg font-bold text-yellow-800 mb-2">パスワードをリセットしました</h3>
              @endif
              <div class="text-yellow-800 space-y-2">
                <p class="font-semibold text-base">
                  @if (session('new_user_created'))
                    初期パスワード：
                  @else
                    新しいパスワード：
                  @endif
                  <span
                    class="inline-block bg-yellow-100 px-3 py-1 rounded border border-yellow-300 font-mono text-lg">{{ session('new_password') }}</span>
                </p>
                <p class="text-sm">⚠️ このパスワードをユーザーに伝えてください。</p>
                <p class="text-sm font-bold text-red-600">⚠️ 画面を閉じると再表示できません。</p>
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- 成功メッセージ -->
      @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ session('success') }}</span>
        </div>
      @endif

      <!-- エラーメッセージ -->
      @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ session('error') }}</span>
        </div>
      @endif

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
                    ユーザー管理者
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
            <a href="{{ route('admin.users.edit', $user) }}"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
              編集
            </a>
            <form method="POST" action="{{ route('admin.users.resetPassword', $user) }}"
              onsubmit="return confirm('このユーザーのパスワードをリセットしますか？\nパスワードはランダムな8文字英数字になります。');">
              @csrf
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                パスワードリセット
              </button>
            </form>
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
