<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Http\Resources\StudentResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ExamResultResource;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use function PHPUnit\Framework\callback;

class StudentController extends Controller
{
    // GET /api/student/{code}
    public function show($code)
    {
        $student = Student::where('code', $code)->first();
        if (!$student) {
            return response()->json(['success' => false, 'error' => 'Student not found'], 404);
        }

        return [
            'success' => true,
            'data' => new StudentResource($student)
        ];
    }

    // GET /api/student/{code}/exams
    public function exams($code)
    {
        $student = Student::where('code', $code)->first();
        if (!$student) {
            return response()->json(['success' => false, 'error' => 'Student not found'], 404);
        }

        $exams = Exam::whereHas('subject.batches', function ($q) use ($student) {
            $q->where('batches.id', $student->batch_id);
        })
        ->doesntHave(relation: 'examResults' , callback: function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
        ->get();

        $exams_result = ExamResult::where('student_id', $student->id)->get();

        return (object) [
            'success' => true,
            'data' => [
                'available' => ExamResource::collection($exams),
                'taken' => ExamResultResource::collection($exams_result),
            ]

        ];
    }
}
