<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Http\Resources\QuestionResource;

class ExamController extends Controller
{
    public function show($examId)
    {
        $exam = Exam::with('questions')->find($examId);
        if (!$exam) {
            return response()->json([
                'success' => false,
                'error' => 'Exam not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => QuestionResource::collection($exam->questions)
        ]);
    }
}
