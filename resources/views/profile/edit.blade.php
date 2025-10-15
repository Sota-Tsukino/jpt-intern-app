<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- ユーザー情報表示 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            ユーザー情報
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            あなたのアカウント情報です。
                        </p>
                    </header>

                    <div class="mt-6 space-y-4">
                        <!-- 氏名 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">氏名</label>
                            <p class="mt-1 text-gray-900">{{ Auth::user()->name }}</p>
                        </div>

                        <!-- メールアドレス -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                            <p class="mt-1 text-gray-900">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- 学年・クラス -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">学年・クラス</label>
                            <p class="mt-1 text-gray-900">
                                @if (Auth::user()->class)
                                    {{ Auth::user()->class->grade }}年{{ Auth::user()->class->class_name }}組
                                @else
                                    <span class="text-gray-400">未配置</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- パスワード更新 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- ホームに戻るボタン -->
            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    @if (Auth::user()->role === 'student')
                        <a href="{{ route('student.home') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ホームに戻る
                        </a>
                    @elseif (Auth::user()->role === 'teacher')
                        <a href="{{ route('teacher.home') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ホームに戻る
                        </a>
                    @elseif (Auth::user()->role === 'admin')
                        <a href="{{ route('admin.home') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ホームに戻る
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
