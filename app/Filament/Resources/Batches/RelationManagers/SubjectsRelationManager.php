<?php

namespace App\Filament\Resources\Batches\RelationManagers;

use App\Filament\Resources\Batches\BatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = 'Subjects';
    protected static ?string $relatedResource = BatchResource::class;

    protected static ?string $pluralLabel = 'Subjects';

   

    public function table(Table $table): Table
    {
        return $table
            ->actions([])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
