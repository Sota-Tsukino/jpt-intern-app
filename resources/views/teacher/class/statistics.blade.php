<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          クラス全体の統計グラフ
        </h2>
        <div class="text-sm text-gray-600 mt-1">
          @if ($teacher->class)
            {{ $teacher->class->grade }}年{{ $teacher->class->class_name }}組 - 過去30日間のデータ
          @endif
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          @if (count($dates) > 0)
            <!-- グラフ表示 -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">体調・メンタルの平均値推移</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <canvas id="classStatisticsChart" style="max-height: 400px;"></canvas>
              </div>
            </div>

            <!-- 提出数推移グラフ -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">日別提出数の推移</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <canvas id="submissionChart" style="max-height: 300px;"></canvas>
              </div>
            </div>

            <!-- 統計サマリー -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">体調平均</h4>
                <p class="text-2xl font-bold text-blue-600">
                  {{ number_format(collect($healthData)->avg(), 2) }}
                </p>
                <p class="text-xs text-blue-600 mt-1">過去30日間</p>
              </div>
              <div class="bg-purple-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-purple-800 mb-2">メンタル平均</h4>
                <p class="text-2xl font-bold text-purple-600">
                  {{ number_format(collect($mentalData)->avg(), 2) }}
                </p>
                <p class="text-xs text-purple-600 mt-1">過去30日間</p>
              </div>
              <div class="bg-green-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-800 mb-2">平均提出数</h4>
                <p class="text-2xl font-bold text-green-600">
                  {{ number_format(collect($submissionCounts)->avg(), 1) }}
                </p>
                <p class="text-xs text-green-600 mt-1">1日あたり</p>
              </div>
            </div>
          @else
            <div class="text-center py-12">
              <p class="text-gray-500">過去30日間のデータがありません。</p>
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

  @if (count($dates) > 0)
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // 体調・メンタル推移グラフ
        const ctx1 = document.getElementById('classStatisticsChart');
        new Chart(ctx1, {
          type: 'line',
          data: {
            labels: @json($dates),
            datasets: [
              {
                label: '体調平均',
                data: @json($healthData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
              },
              {
                label: 'メンタル平均',
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
            maintainAspectRatio: true,
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
                    const value = context.parsed.y;
                    const roundedValue = Math.round(value);
                    return context.dataset.label + ': ' + value.toFixed(2) + ' (' + labels[roundedValue] + ')';
                  }
                }
              }
            }
          }
        });

        // 提出数推移グラフ
        const ctx2 = document.getElementById('submissionChart');
        new Chart(ctx2, {
          type: 'bar',
          data: {
            labels: @json($dates),
            datasets: [
              {
                label: '提出数',
                data: @json($submissionCounts),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
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
              }
            }
          }
        });
      });
    </script>
  @endif
</x-app-layout>
