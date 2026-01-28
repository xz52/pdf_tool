<?php

namespace App\Filament\Resources\GlobalQuestions\Pages;

use App\Filament\Resources\GlobalQuestions\GlobalQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalQuestion extends CreateRecord
{
    protected static string $resource = GlobalQuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['options'] = collect($data['options'])->pluck('option_text')->toArray();

        return $data;
    }
}
