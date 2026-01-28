<?php

namespace App\Filament\Resources\Batches\RelationManagers;

use App\Filament\Resources\Batches\BatchResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'Students';

    protected static ?string $relatedResource = BatchResource::class;

  

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('name'),
            ])
            ->actions([
              
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
