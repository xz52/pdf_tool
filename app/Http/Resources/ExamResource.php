<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'duration' => $this->duration,
            'totalQuestions' => $this->total_questions,
            'isActive' => (bool) $this->is_active,
            'description' => $this->description,
        ];
    }
}
