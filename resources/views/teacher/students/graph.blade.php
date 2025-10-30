<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ $user->name }}さんの体調・メンタル推移
        </h2>
        <div class="text-sm text-gray-600 mt-1">
          {{ $startDate->format('Y/m/d') }} ～ {{ $endDate->format('Y/m/d') }}
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 日付指定フォーム -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">期間指定</h3>
          <form method="GET" action="{{ route('teacher.students.graph', $user) }}" class="flex flex-wrap gap-4 items-end">
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">開始日</label>
              <input type="date" id="start_date" name="start_date"
                value="{{ $startDate->format('Y-m-d') }}"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            </div>
            <div>
              <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">終了日</label>
              <input type="date" id="end_date" name="end_date"
                value="{{ $endDate->format('Y-m-d') }}"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            </div>
            <div>
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                表示
              </button>
            </div>
            <div>
              <a href="{{ route('teacher.students.graph', $user) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                リセット
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <!-- 生徒情報 -->
          <div class="mb-6 pb-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">生徒氏名</label>
                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">学年・クラス</label>
                <p class="text-lg text-gray-900">
                  @if ($user->class)
                    {{ $user->class->grade }}年{{ $user->class->class_name }}組
                  @endif
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">データ件数</label>
                <p class="text-lg text-gray-900">{{ $entries->count() }}件</p>
              </div>
            </div>
          </div>

          @if ($entries->count() > 0)
            <!-- 注意書き -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
              <p class="text-sm text-blue-700">
                <span class="font-semibold">※ グラフについて：</span>
                連絡帳の提出データが存在する日のみ表示されます。指定期間内でもデータがない日は表示されません。
              </p>
            </div>

            <!-- グラフ表示 -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">体調・メンタルの推移</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div style="position: relative; height: 300px; min-height: 300px;">
                  <canvas id="healthChart"></canvas>
                </div>
              </div>
            </div>

            <!-- データテーブル -->
            <div>
              <h3 class="text-lg font-semibold text-gray-800 mb-4">詳細データ</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">記録対象日</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">体調</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メンタル</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">スタンプ</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($entries->reverse() as $entry)
                      <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ \Carbon\Carbon::parse($entry->entry_date)->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                          <span class="font-semibold {{ $entry->health_status >= 4 ? 'text-green-600' : ($entry->health_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $entry->health_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->health_status] }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                          <span class="font-semibold {{ $entry->mental_status >= 4 ? 'text-green-600' : ($entry->mental_status === 3 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $entry->mental_status }}:{{ ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'][$entry->mental_status] }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                          @if ($entry->stamp_type)
                            @if ($entry->stamp_type === 'good') 👍
                            @elseif ($entry->stamp_type === 'great') ⭐
                            @elseif ($entry->stamp_type === 'fighting') 💪
                            @elseif ($entry->stamp_type === 'care') 💙
                            @endif
                          @else
                            <span class="text-gray-400">未読</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @else
            <div class="text-center py-12">
              <p class="text-gray-500">指定期間のデータがありません。</p>
            </div>
          @endif

          <!-- 戻るボタン -->
          <div class="mt-6 pt-6 border-t">
            <a href="{{ route('teacher.home') }}"
              class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              ホームに戻る
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($entries->count() > 0)
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('healthChart');
        new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($dates),
          datasets: [
            {
              label: '体調',
              data: @json($healthData),
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.3,
              fill: true
            },
            {
              label: 'メンタル',
              data: @json($mentalData),
              borderColor: 'rgb(147, 51, 234)',
              backgroundColor: 'rgba(147, 51, 234, 0.1)',
              tension: 0.3,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: false,
              min: 1,
              max: 5,
              ticks: {
                stepSize: 1,
                callback: function(value) {
                  const labels = ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'];
                  return value + ': ' + labels[value];
                }
              }
            },
            x: {
              title: {
                display: true,
                text: '記録対象日'
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const labels = ['', 'とても悪い', '悪い', '普通', '良い', 'とても良い'];
                  return context.dataset.label + ': ' + context.parsed.y + ' (' + labels[context.parsed.y] + ')';
                }
              }
            }
          }
        }
      });
      });
    </script>
  @endif
</x-app-layout>
