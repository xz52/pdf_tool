<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalQuestion extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['question_text', 'options', 'correct_option', 'explanation', 'subject_id', 'type'];
    protected $casts = [
        'options' => 'array',
    ];

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
}
