<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('batch.name')
                    ->sortable(),
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
                    ->visible(fn() => hexa()->can('student.view')),
                EditAction::make()
                    ->visible(fn() => hexa()->can('student.update')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => hexa()->can('student.delete')),
                ]),
            ]);
    }
}
