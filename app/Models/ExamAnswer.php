<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_result_id',
        'question_id',
        'question_text',
        'options',
        'selected_option_index',
        'correct_option_index',
        'is_correct',
        'explanation'
    ];

    protected $casts = [
        'options' => 'array',
        'is_correct' => 'boolean',
    ];

    public function examResult() {
        return $this->belongsTo(ExamResult::class);
    }

    public function question() {
        return $this->belongsTo(ExamQuestion::class);
    }
}
