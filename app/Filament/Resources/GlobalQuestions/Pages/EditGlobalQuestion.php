<?php

namespace App\Filament\Resources\GlobalQuestions\Pages;

use App\Filament\Resources\GlobalQuestions\GlobalQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Enums\QuestionTypesEnum;

class EditGlobalQuestion extends EditRecord
{
    protected static string $resource = GlobalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->visible(fn() => hexa()->can('question.view')),
            DeleteAction::make()
                ->visible(fn() => hexa()->can('question.delete')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Transform database flat structure to Filament's 'question_options' repeater structure
        // 'T_F' in DB -> QuestionTypesEnum::T_F
        // 'choose' in DB -> QuestionTypesEnum::CHOOSE
        
        $type = $data['type'] ?? 'choose';
        $enumType = ($type === 'T_F' || $type === 'true_false') ? QuestionTypesEnum::T_F : QuestionTypesEnum::CHOOSE;

        $data['question_options'] = [[
            'type' => $enumType->value,
            'correct_option' => $data['correct_option'] ?? 0,
            'options' => collect($data['options'] ?? [])->map(fn($opt) => ['option_text' => $opt])->toArray()
        ]];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['question_options']) && count($data['question_options']) > 0) {
            $model = $data['question_options'][0];
            
            $data['type'] = $model['type'];
            $data['correct_option'] = $model['correct_option'];
            
            if ($model['type'] === QuestionTypesEnum::CHOOSE->value) {
                $data['options'] = collect($model['options'])->pluck('option_text')->toArray();
            } else {
                $data['options'] = ['YES', 'NO'];
                $data['type'] = 'T_F';
            }
        }

        unset($data['question_options']);
        return $data;
    }
}
