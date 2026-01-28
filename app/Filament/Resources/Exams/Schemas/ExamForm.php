<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Models\Batch;
use App\Models\ExamQuestion;
use App\Models\GlobalQuestion;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use SebastianBergmann\CodeCoverage\Test\Target\Function_;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('batch_id')
                    ->label('Batch')
                    ->options(\App\Models\Batch::all()->pluck('name', 'id'))
                    ->live(),

                Select::make('subject_id')
                    ->label('Subject')
                    ->options(function (Get $get) {
                        $batch_id = $get('batch_id');

                        return Batch::find($batch_id)
                            ?->subjects()
                            ->select('subjects.id', 'subjects.name')
                            ->pluck('name', 'subjects.id');
                    })
                    ->required()
                    ->live(),

                TextInput::make('subject')
                    ->label('Exam Title')
                    ->required(),

                TextInput::make('duration')
                    ->label('Duration')
                    ->numeric()
                    ->required(),

                TextInput::make('total_questions')
                    ->label('Total Questions Number')
                    ->numeric()
                    ->required()
                    ->default(50)
                    ->live()
                    ->hint(function (Get $get) {
                        $count = GlobalQuestion::where('subject_id', $get('subject_id'))->count();

                        return "please select a number between 1 and {$count}.";
                    })
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        return GlobalQuestion::where('subject_id', $get('subject_id'))->count();
                    }),



                Checkbox::make('is_random')
                    ->label('Random Questions')
                    ->hint('If remove this option , you can select questions manually')
                    ->default(true)
                    ->live(),

                Select::make('questions')
                    ->label('Questions')
                    ->multiple()
                    ->options(function (Get $get) {
                        return GlobalQuestion::where('subject_id', $get('subject_id'))->pluck('question_text', 'id');
                    })
                    ->minItems(fn(Get $get) => $get('total_questions'))
                    ->maxItems(fn(Get $get) => $get('total_questions'))
                    ->visible(fn(Get $get) => !$get('is_random'))
                    ->columnSpanFull()
                    ->required(),

                Textarea::make('description')
                    ->columnSpanFull(),





            ]);
    }
}
