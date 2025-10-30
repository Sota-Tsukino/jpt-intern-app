<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentCountPerClass = 30;
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

        // 副担任 × 6（各クラス1名）
        $subTeachers = [
            ['name' => '鈴木副担任', 'class_id' => 1, 'grade' => 1, 'class' => 'A'],
            ['name' => '木村副担任', 'class_id' => 2, 'grade' => 1, 'class' => 'B'],
            ['name' => '小林副担任', 'class_id' => 3, 'grade' => 2, 'class' => 'A'],
            ['name' => '加藤副担任', 'class_id' => 4, 'grade' => 2, 'class' => 'B'],
            ['name' => '吉田副担任', 'class_id' => 5, 'grade' => 3, 'class' => 'A'],
            ['name' => '渡辺副担任', 'class_id' => 6, 'grade' => 3, 'class' => 'B'],
        ];

        foreach ($subTeachers as $subTeacher) {
            User::create([
                'name' => $subTeacher['name'],
                'email' => "subteacher{$subTeacher['grade']}{$subTeacher['class']}@example.com",
                'password' => Hash::make('password'),
                'role' => 'sub_teacher',
                'class_id' => $subTeacher['class_id'],
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
}
