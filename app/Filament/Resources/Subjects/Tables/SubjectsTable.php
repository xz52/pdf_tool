<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
             
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ,
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ,
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn() => hexa()->can('subject.view')),
                EditAction::make()
                    ->visible(fn() => hexa()->can('subject.update')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => hexa()->can('subject.delete')),
                ]),
            ]);
    }
}
