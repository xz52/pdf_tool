<?php

namespace App\Filament\Resources\GlobalQuestions\Pages;

use App\Filament\Resources\GlobalQuestions\GlobalQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlobalQuestions extends ListRecords
{
    protected static string $resource = GlobalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Question')
                ->visible(fn() => hexa()->can('question.create')),
        ];
    }
}
