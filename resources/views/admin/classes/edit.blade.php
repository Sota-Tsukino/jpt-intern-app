<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      クラス編集
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <form method="POST" action="{{ route('admin.classes.update', $class) }}">
            @csrf
            @method('PUT')

            <!-- 学年 -->
            <div class="mb-4">
              <label for="grade" class="block text-sm font-medium text-gray-700 mb-1">学年 <span
                  class="text-red-600">*</span></label>
              <select name="grade" id="grade" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('grade') border-red-500 @enderror">
                <option value="">選択してください</option>
                <option value="1" {{ old('grade', $class->grade) == '1' ? 'selected' : '' }}>1年</option>
                <option value="2" {{ old('grade', $class->grade) == '2' ? 'selected' : '' }}>2年</option>
                <option value="3" {{ old('grade', $class->grade) == '3' ? 'selected' : '' }}>3年</option>
              </select>
              @error('grade')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- クラス名 -->
            <div class="mb-4">
              <label for="class_name" class="block text-sm font-medium text-gray-700 mb-1">クラス名 <span
                  class="text-red-600">*</span></label>
              <input type="text" name="class_name" id="class_name"
                value="{{ old('class_name', $class->class_name) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('class_name') border-red-500 @enderror"
                placeholder="例: A">
              @error('class_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- 担任 -->
            <div class="mb-6">
              <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">担任割り当て</label>
              <select name="teacher_id" id="teacher_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('teacher_id') border-red-500 @enderror">
                <option value="">未配置</option>
                @foreach ($teachers as $teacher)
                  <option value="{{ $teacher->id }}"
                    {{ old('teacher_id', $class->teacher?->id) == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                  </option>
                @endforeach
              </select>
              <p class="mt-1 text-sm text-gray-500">※担当クラスが未割当の担任のみ選択できます。</p>
              @error('teacher_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center">
              <div class="flex gap-2">
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  更新
                </button>
                <a href="{{ route('admin.classes.index') }}"
                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  キャンセル
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
