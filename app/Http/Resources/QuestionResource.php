<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'examId' => $this->exam_id,
            'questionText' => $this->question_text,
            'options' => $this->options,
            'correctOptionIndex' => $this->correct_option,
            'explanation' => $this->explanation,
        ];
    }
}
