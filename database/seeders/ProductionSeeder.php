<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * 本番用/デモ用Seeder（大量データ）
     *
     * データ量:
     * - クラス: 6クラス
     * - 管理者: 1人
     * - 担任: 6人（各クラス1名）
     * - 生徒: 180人（各クラス30名）
     * - 連絡帳: 約550件
     *   - 生徒1人（デモ用）: 30件（推移グラフ確認用）
     *   - その他の生徒: 3件ずつ
     *
     * 所要時間: 約30秒〜1分
     */
    public function run(): void
    {
        // 1. クラス作成
        $this->call(ClassSeeder::class);

        // 2. ユーザー作成
        $this->createUsers(30); // 各クラス30名

        // 3. 連絡帳作成
        $this->createEntries(30, 3); // 1人目30件、残り3件ずつ
    }

    /**
     * ユーザーを作成
     *
     * @param int $studentCountPerClass 各クラスの生徒数
     */
    private function createUsers(int $studentCountPerClass): void
    {
        // 管理者 × 1
        User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'class_id' => null,
        ]);

        // 担任 × 6（各クラス1名）
        $teachers = [
            ['name' => '田中先生', 'class_id' => 1, 'grade' => 1, 'class' => 'A'],
            ['name' => '佐藤先生', 'class_id' => 2, 'grade' => 1, 'class' => 'B'],
            ['name' => '高橋先生', 'class_id' => 3, 'grade' => 2, 'class' => 'A'],
            ['name' => '伊藤先生', 'class_id' => 4, 'grade' => 2, 'class' => 'B'],
            ['name' => '山本先生', 'class_id' => 5, 'grade' => 3, 'class' => 'A'],
            ['name' => '中村先生', 'class_id' => 6, 'grade' => 3, 'class' => 'B'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => "teacher{$teacher['grade']}{$teacher['class']}@example.com",
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'class_id' => $teacher['class_id'],
            ]);
        }

        // 生徒（各クラス $studentCountPerClass 名）
        $lastNames = ['山田', '鈴木', '高橋', '田中', '伊藤', '渡辺', '中村', '小林', '加藤', '吉田', '山本', '佐藤', '斉藤', '松本', '井上', '木村', '林', '清水', '山崎', '森', '池田', '橋本', '阿部', '石川', '山口', '中島', '前田', '藤田', '後藤', '長谷川'];
        $firstNames = ['太郎', '花子', '次郎', '美咲', '健太', 'さくら', '大輔', '愛', '翔太', '陽菜', '拓也', '結衣', '雄大', '美優', '翔', '七海', '悠斗', '葵', '大樹', '結菜', '蓮', '心春', '颯太', 'ひなた', '陸', '莉子', '悠真', 'さくら', '優斗', '美月'];

        $grades = [1, 2, 3];
        $classes = ['A', 'B'];
        $classId = 1;

        foreach ($grades as $grade) {
            foreach ($classes as $class) {
                for ($i = 1; $i <= $studentCountPerClass; $i++) {
                    User::create([
                        'name' => $lastNames[($i - 1) % 30] . $firstNames[($i - 1) % 30],
                        'email' => "student{$grade}{$class}" . str_pad($i, 2, '0', STR_PAD_LEFT) . "@example.com",
                        'password' => Hash::make('password'),
                        'role' => 'student',
                        'class_id' => $classId,
                    ]);
                }
                $classId++;
            }
        }
    }

    /**
     * 連絡帳を作成
     *
     * @param int $firstStudentEntryCount 最初の生徒のエントリー数
     * @param int $otherStudentEntryCount その他の生徒のエントリー数
     */
    private function createEntries(int $firstStudentEntryCount, int $otherStudentEntryCount): void
    {
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

                // スタンプ（最新2件以外はスタンプ済み = 既読扱い）
                $hasStamp = $createdEntries < ($entryCount - 2);
                $stampType = $hasStamp ? ['good', 'great', 'fighting', 'care'][rand(0, 3)] : null;
                $stampedAt = $hasStamp ? (clone $submittedAt)->addHours(rand(1, 5)) : null;
                $stampedBy = $hasStamp && $teacher ? $teacher->id : null;

                Entry::create([
                    'user_id' => $student->id,
                    'entry_date' => $entryDate->format('Y-m-d'),
                    'submitted_at' => $submittedAt,
                    'health_status' => rand(1, 5),
                    'mental_status' => rand(1, 5),
                    'study_reflection' => $this->getRandomStudyReflection(),
                    'club_reflection' => rand(0, 1) ? $this->getRandomClubReflection() : null,
                    'stamp_type' => $stampType,
                    'stamped_at' => $stampedAt,
                    'stamped_by' => $stampedBy,
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
