<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstStudentEntryCount = 30;
        $otherStudentEntryCount = 3;
        // 生徒のみを取得
        $students = User::where('role', 'student')->get();

        // 最初の1人だけ1か月分（30件）、残りは3件ずつ
        $isFirstStudent = true;

        foreach ($students as $student) {
            // 担任を取得（同じクラスの教師）
            $teacher = User::where('role', 'teacher')
                ->where('class_id', $student->class_id)
                ->first();

            // 最初の生徒は $firstStudentEntryCount 件、それ以外は $otherStudentEntryCount 件
            $entryCount = $isFirstStudent ? $firstStudentEntryCount : $otherStudentEntryCount;
            $isFirstStudent = false;

            // 記録対象日の開始日を設定（30件の場合は約1.5か月前、3件の場合は10日前）
            $startDaysAgo = $entryCount === 30 ? 45 : 10;

            $createdEntries = 0;
            $currentDay = 0;

            while ($createdEntries < $entryCount) {
                // 記録対象日（過去の日付）
                $entryDate = Carbon::now()->subDays($startDaysAgo - $currentDay);
                $currentDay++;

                // 土日をスキップ
                if ($entryDate->isWeekend()) {
                    continue;
                }

                // 提出日時（記録対象日の翌日）
                $submittedAt = (clone $entryDate)->addDay()->setTime(8, rand(0, 59), rand(0, 59));

                // 既読フラグ（最新2件以外は既読にする）
                $isRead = $createdEntries < ($entryCount - 2);
                $readAt = $isRead ? (clone $submittedAt)->addHours(rand(1, 5)) : null;
                $readBy = $isRead && $teacher ? $teacher->id : null;

                Entry::create([
                    'user_id' => $student->id,
                    'entry_date' => $entryDate->format('Y-m-d'),
                    'submitted_at' => $submittedAt,
                    'health_status' => rand(1, 5),
                    'mental_status' => rand(1, 5),
                    'study_reflection' => $this->getRandomStudyReflection(),
                    'club_reflection' => rand(0, 1) ? $this->getRandomClubReflection() : null,
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                    'read_by' => $readBy,
                ]);

                $createdEntries++;
            }
        }
    }

    /**
     * ランダムな授業振り返りを生成
     */
    private function getRandomStudyReflection(): string
    {
        $reflections = [
            '数学の授業で二次方程式を学びました。解の公式の使い方が理解できました。',
            '英語の授業でリスニングテストがありました。前回より点数が上がって嬉しかったです。',
            '国語の授業で古文を読みました。古典文法が少し難しかったです。',
            '理科の実験で化学反応を観察しました。実際に見ることができて面白かったです。',
            '社会の授業で日本の歴史について学びました。江戸時代の文化に興味を持ちました。',
            '体育でバスケットボールをしました。チームワークの大切さを学びました。',
            '音楽の授業で合唱の練習をしました。みんなで歌うのが楽しかったです。',
            '美術で水彩画を描きました。色の使い方が難しかったですが、先生に褒められました。',
            '技術の授業でプログラミングを学びました。初めてでしたが面白かったです。',
            '家庭科で調理実習をしました。包丁の使い方を練習しました。',
        ];

        return $reflections[array_rand($reflections)];
    }

    /**
     * ランダムな部活振り返りを生成
     */
    private function getRandomClubReflection(): string
    {
        $reflections = [
            'サッカー部の練習でシュート練習をしました。次の試合に向けて頑張ります。',
            '吹奏楽部で文化祭に向けた曲の練習をしました。難しいパートですが頑張ります。',
            'バスケ部で新しい戦術を学びました。チームメイトとの連携が良くなってきました。',
            '美術部でコンクール用の作品を制作しました。完成まであと少しです。',
            '野球部で素振りと守備練習をしました。レギュラーを目指して頑張ります。',
            '合唱部で発声練習をしました。高音が少しずつ出るようになってきました。',
            'テニス部でサーブの練習をしました。先輩にアドバイスをもらいました。',
            '演劇部で台本の読み合わせをしました。自分の役に感情を込められるよう練習します。',
        ];

        return $reflections[array_rand($reflections)];
    }
}
