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
        'is_read',
        'read_at',
        'read_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'submitted_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * リレーション: 投稿した生徒
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーション: 既読処理した教師
     */
    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }
}
