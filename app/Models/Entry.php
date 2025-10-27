<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entry_date',
        'submitted_at',
        'health_status',
        'mental_status',
        'study_reflection',
        'club_reflection',
        // 課題2追加カラム
        'stamp_type',
        'stamped_at',
        'teacher_feedback',
        'commented_at',
        'flag',
        'flagged_at',
        'flagged_by',
        'flag_memo',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'submitted_at' => 'datetime',
        // 課題2追加カラム
        'stamped_at' => 'datetime',
        'commented_at' => 'datetime',
        'flagged_at' => 'datetime',
    ];

    /**
     * リレーション: 投稿した生徒
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーション: フラグを設定した教師
     */
    public function flagger()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }
}
