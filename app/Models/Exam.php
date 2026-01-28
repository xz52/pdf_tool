<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['subject_id', 'subject', 'duration', 'total_questions', 'is_active', 'description'];

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function questions() {
        return $this->hasMany(ExamQuestion::class);
    }

    public function examResults() {
        return $this->hasMany(ExamResult::class);
    }
}
