<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code'];

    public function batches() {
        return $this->belongsToMany(Batch::class, 'batch_subject');
    }

    public function exams() {
        return $this->hasMany(Exam::class);
    }

    public function globalQuestions() {
        return $this->hasMany(GlobalQuestion::class);
    }
}
