<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'examResults';

    protected static ?string $relatedResource = ExamResource::class;

    protected static ?string $title = 'exam results';

    public function isReadOnly(): bool
    {
        return false;
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name'),
                TextColumn::make('exam_subject'),
                TextColumn::make('score'),
                TextColumn::make('total_questions'),

            ])
            ->actions([
                
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('Export'),
            ]);
    }
    
}
