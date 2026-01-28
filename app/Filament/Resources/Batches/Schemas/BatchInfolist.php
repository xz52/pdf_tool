<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
