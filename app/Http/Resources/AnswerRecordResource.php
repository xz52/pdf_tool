<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'questionId' => (string) $this->question_id,
            'questionText' => $this->question_text,
            'options' => $this->options,
            'selectedOptionIndex' => $this->selected_option_index,
            'correctOptionIndex' => $this->correct_option_index,
            'isCorrect' => (bool) $this->is_correct,
            'explanation' => $this->explanation,
        ];
    }
}
