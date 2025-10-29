<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      ユーザー編集
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- 名前 -->
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">名前 <span
                  class="text-red-600">*</span></label>
              <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                placeholder="例: 山田太郎">
              @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- メールアドレス -->
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">メールアドレス <span
                  class="text-red-600">*</span></label>
              <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                placeholder="例: user@example.com">
              @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- ロール -->
            <div class="mb-4">
              <label for="role" class="block text-sm font-medium text-gray-700 mb-1">ロール <span
                  class="text-red-600">*</span></label>
              <select name="role" id="role" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('role') border-red-500 @enderror">
                <option value="">選択してください</option>
                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>生徒</option>
                <option value="teacher" {{ old('role', $user->role) === 'teacher' ? 'selected' : '' }}>担任</option>
                <option value="sub_teacher" {{ old('role', $user->role) === 'sub_teacher' ? 'selected' : '' }}>副担任</option>
              </select>
              @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- 学年・クラス -->
            <div class="mb-6">
              <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">学年・クラス</label>
              <select name="class_id" id="class_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('class_id') border-red-500 @enderror">
                <option value="">未割当</option>
                @foreach ($classes as $class)
                  <option value="{{ $class->id }}"
                    {{ old('class_id', $user->class_id) == $class->id ? 'selected' : '' }}>
                    {{ $class->grade }}年{{ $class->class_name }}組
                  </option>
                @endforeach
              </select>
              @error('class_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- ボタン -->
            <div class="flex gap-2">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                更新
              </button>
              <a href="{{ route('admin.users.show', $user) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                キャンセル
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
