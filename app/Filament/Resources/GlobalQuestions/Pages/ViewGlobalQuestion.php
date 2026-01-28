<?php

namespace App\Filament\Resources\GlobalQuestions\Pages;

use App\Filament\Resources\GlobalQuestions\GlobalQuestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGlobalQuestion extends ViewRecord
{
    protected static string $resource = GlobalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
