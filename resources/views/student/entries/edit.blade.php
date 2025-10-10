<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          連絡帳編集
        </h2>
        <div class="text-sm text-gray-600 mt-1">
          <span class="font-medium">今日:</span>
          {{ \Carbon\Carbon::now()->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::now()->dayOfWeek] . '）') }}
        </div>
      </div>
      <a href="{{ route('student.entries.show', $entry) }}"
        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        キャンセル
      </a>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <form method="POST" action="{{ route('student.entries.update', $entry) }}">
            @csrf
            @method('PATCH')

            <!-- 記録対象日（表示のみ） -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">記録対象日</label>
              <p class="text-lg font-semibold text-gray-900">
                {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($entry->entry_date)->dayOfWeek] . '）') }}
              </p>
            </div>

            <!-- 体調 -->
            <div class="mb-6">
              <label for="health_status" class="block text-sm font-medium text-gray-700 mb-2">
                体調 <span class="text-red-600">*</span>
              </label>
              <div class="space-y-2">
                @foreach ([5 => '良好', 4 => '良い', 3 => '普通', 2 => '悪い', 1 => '最悪'] as $value => $label)
                  <label class="inline-flex items-center mr-6">
                    <input type="radio" name="health_status" value="{{ $value }}"
                      {{ old('health_status', $entry->health_status) == $value ? 'checked' : '' }}
                      class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2">{{ $value }} - {{ $label }}</span>
                  </label>
                @endforeach
              </div>
              @error('health_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- メンタル -->
            <div class="mb-6">
              <label for="mental_status" class="block text-sm font-medium text-gray-700 mb-2">
                メンタル <span class="text-red-600">*</span>
              </label>
              <div class="space-y-2">
                @foreach ([5 => '良好', 4 => '良い', 3 => '普通', 2 => '悪い', 1 => '最悪'] as $value => $label)
                  <label class="inline-flex items-center mr-6">
                    <input type="radio" name="mental_status" value="{{ $value }}"
                      {{ old('mental_status', $entry->mental_status) == $value ? 'checked' : '' }}
                      class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2">{{ $value }} - {{ $label }}</span>
                  </label>
                @endforeach
              </div>
              @error('mental_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- 授業振り返り -->
            <div class="mb-6">
              <label for="study_reflection" class="block text-sm font-medium text-gray-700 mb-2">
                授業振り返り <span class="text-red-600">*</span>
                <span class="text-sm text-gray-500">(最大500文字)</span>
              </label>
              <textarea name="study_reflection" id="study_reflection" rows="5"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                maxlength="500" required>{{ old('study_reflection', $entry->study_reflection) }}</textarea>
              @error('study_reflection')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- 部活振り返り -->
            <div class="mb-6">
              <label for="club_reflection" class="block text-sm font-medium text-gray-700 mb-2">
                部活振り返り
                <span class="text-sm text-gray-500">(任意・最大500文字)</span>
              </label>
              <textarea name="club_reflection" id="club_reflection" rows="5"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                maxlength="500">{{ old('club_reflection', $entry->club_reflection) }}</textarea>
              @error('club_reflection')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- 注意事項 -->
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <p class="text-sm text-yellow-800">
                <strong>注意:</strong> 担任が既読処理を行った後は、この連絡帳を編集できなくなります。
              </p>
            </div>

            <!-- ボタン -->
            <div class="flex gap-4">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                更新
              </button>
              <a href="{{ route('student.entries.show', $entry) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                キャンセル
              </a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
