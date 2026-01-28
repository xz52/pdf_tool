<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = ['student_id', 'exam_id', 'exam_subject', 'score', 'total_questions', 'submitted_at'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];
    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function answers() {
        return $this->hasMany(ExamAnswer::class);
    }
}

