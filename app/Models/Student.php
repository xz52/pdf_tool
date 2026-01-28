<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['code', 'name', 'batch_id'];

    public function batch() {
        return $this->belongsTo(Batch::class);
    }

    public function examResults() {
        return $this->hasMany(ExamResult::class);
    }
}
