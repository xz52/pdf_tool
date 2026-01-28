<?php

namespace App\Filament\Resources\GlobalQuestions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GlobalQuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('question_text'),
                TextEntry::make('explanation'),
                TextEntry::make('correct_option')
                    ->numeric(),
                TextEntry::make('subject_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
