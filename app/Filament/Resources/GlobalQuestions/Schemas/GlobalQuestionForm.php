<?php

namespace App\Filament\Resources\GlobalQuestions\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Ramsey\Collection\Set;

class GlobalQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question_text')
                    ->required(),
                TextInput::make('explanation')
                    ->required(),
                Select::make('subject_id')
                    ->required()
                    ->label('Subject')
                    ->options(\App\Models\Subject::all()->pluck('name', 'id')),

                Select::make('correct_option')
                    ->required()
                   
                    ->label('Correct Option')
                    ->visible(function (Get $get) {
                        $options = collect($get('options'))->pluck('option_text');

                        foreach ($options as $option) {
                            if ($option == null || $option == '') {
                                return false;
                            }
                        }
                        return true;
                    })
                    ->options(function (Get $get) {
                        $options = collect($get('options'))->pluck('option_text');

                        return $options;
                    })
                    ->live(),

                Repeater::make('options')
                    ->label('Options')
                    ->columnSpanFull()
                    ->grid(2)
                    ->schema([
                        TextInput::make('option_text')
                            ->required()
                            ->label('Option'),
                    ])
                    ->deletable(false)
                    ->defaultItems(4)
                    ->maxItems(4)
                    ->required()
                    ->live(),





            ]);
    }
}
