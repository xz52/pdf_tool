<?php

namespace App\Filament\Resources\GlobalQuestions\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use App\Enums\QuestionTypesEnum;
use Filament\Forms\Get;

class GlobalQuestionForm
{
    public static function configure(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('question_text')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('explanation')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('subject_id')
                            ->required()
                            ->label('Subject')
                            ->options(\App\Models\Subject::all()->pluck('name', 'id'))
                            ->searchable(),
                    ]),

                Section::make('Question Models')
                    ->schema([
                        Repeater::make('question_options')
                            ->label('')
                            ->addable(false)
                            ->deletable(false)
                            ->schema([
                                Select::make('type')
                                    ->options(QuestionTypesEnum::class)
                                    ->required()
                                    ->live()
                                    ->native(false),

                                // Options for Multiple Choice
                                Repeater::make('options')
                                    ->visible(fn (Get $get) => $get('type') === QuestionTypesEnum::CHOOSE->value)
                                    ->schema([
                                        TextInput::make('option_text')
                                            ->required()
                                            ->label('Option Text'),
                                    ])
                                    ->grid(2)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->itemLabel(fn (array $state): ?string => $state['option_text'] ?? null),

                                // Index for Multiple Choice
                                Select::make('correct_option')
                                    ->label('Correct Answer')
                                    ->visible(fn (Get $get) => $get('type') === QuestionTypesEnum::CHOOSE->value)
                                    ->options(function (Get $get) {
                                        $options = $get('options') ?? [];
                                        $choices = [];
                                        foreach ($options as $index => $opt) {
                                            $choices[$index] = "Option " . (chr(65 + $index)) . ": " . ($opt['option_text'] ?? '');
                                        }
                                        return $choices;
                                    })
                                    ->required(fn (Get $get) => $get('type') === QuestionTypesEnum::CHOOSE->value),

                                // Selector for True/False
                                Select::make('correct_option')
                                    ->label('Correct Answer (YES/NO)')
                                    ->visible(fn (Get $get) => $get('type') === QuestionTypesEnum::T_F->value)
                                    ->options([
                                        0 => 'YES (True)',
                                        1 => 'NO (False)',
                                    ])
                                    ->required(fn (Get $get) => $get('type') === QuestionTypesEnum::T_F->value),
                            ])
                    ])
            ]);
    }
}
