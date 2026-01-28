<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use App\Http\Resources\ExamResultResource;
use Carbon\Carbon;

class ExamResultController extends Controller
{
    public function submit(Request $request, $examId)
    {
        $request->validate([
            'studentCode' => 'required|string',
            'answers' => 'required|array'
        ]);

        $student = Student::where('code', $request->studentCode)->first();
        $exam = Exam::with('questions')->find($examId);

        if (!$student || !$exam) {
            return response()->json([
                'success' => false,
                'error' => 'Student or Exam not found'
            ], 404);
        }

        $examResult = ExamResult::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'exam_subject' => $exam->subject,
            'score' => 0,
            'total_questions' => count($exam->questions),
            'submitted_at' => Carbon::now()
        ]);

        $score = 0;
        foreach ($exam->questions as $question) {
            $answerObj = collect($request->answers)->firstWhere('questionId', $question->id);
            $selectedIndex = $answerObj['selectedOptionIndex'] ?? -1; // لو ما اخترش → -1
            $isCorrect = $selectedIndex === $question->correct_option;
            if ($isCorrect) $score++;

            ExamAnswer::create([
                'exam_result_id' => $examResult->id,
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'options' => $question->options,
                'selected_option_index' => $selectedIndex,
                'correct_option_index' => $question->correct_option,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation
            ]);
        }


        $examResult->update(['score' => $score]);

        return response()->json([
            'success' => true,
            'data' => new ExamResultResource($examResult->load('answers'))
        ]);
    }
}
