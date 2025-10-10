<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['grade' => 1, 'class_name' => 'A'],
            ['grade' => 1, 'class_name' => 'B'],
            ['grade' => 2, 'class_name' => 'A'],
            ['grade' => 2, 'class_name' => 'B'],
            ['grade' => 3, 'class_name' => 'A'],
            ['grade' => 3, 'class_name' => 'B'],
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}
