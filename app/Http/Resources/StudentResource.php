<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'batch' => $this->batch?->name, // اسم الدفعة
        ];
    }
}
