<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'studentCode' => $this->student->code,
            'examId' => (string) $this->exam_id,
            'examSubject' => $this->exam_subject,
            'score' => $this->score,
            'totalQuestions' => $this->total_questions,
            'answers' => AnswerRecordResource::collection($this->answers),
            'submittedAt' => $this->submitted_at->toIso8601String(),
        ];
    }
}
