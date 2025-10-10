<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'grade',
        'class_name',
    ];

    /**
     * リレーション: 所属する生徒
     */
    public function students()
    {
        return $this->hasMany(User::class, 'class_id')->where('role', 'student');
    }

    /**
     * リレーション: 担任
     */
    public function teacher()
    {
        return $this->hasOne(User::class, 'class_id')->where('role', 'teacher');
    }

    /**
     * リレーション: 所属するユーザー（生徒・担任）
     */
    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
