<?php

namespace App\Filament\Resources\GlobalQuestions;

use App\Filament\Resources\GlobalQuestions\Pages\CreateGlobalQuestion;
use App\Filament\Resources\GlobalQuestions\Pages\EditGlobalQuestion;
use App\Filament\Resources\GlobalQuestions\Pages\ListGlobalQuestions;
use App\Filament\Resources\GlobalQuestions\Pages\ViewGlobalQuestion;
use App\Filament\Resources\GlobalQuestions\Schemas\GlobalQuestionForm;
use App\Filament\Resources\GlobalQuestions\Schemas\GlobalQuestionInfolist;
use App\Filament\Resources\GlobalQuestions\Tables\GlobalQuestionsTable;
use App\Models\GlobalQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;

class GlobalQuestionResource extends Resource
{
    use HasHexaLite;

    public function defineGates(): array
    {
        return [
            'question.index' => __('Allows viewing the question list'),
            'question.create' => __('Allows creating a new question'),
            'question.update' => __('Allows updating questions'),
            'question.delete' => __('Allows deleting questions'),
        ];
    }
    protected static ?string $model = GlobalQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'question_text';

    public static function form(Schema $schema): Schema
    {
        return GlobalQuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GlobalQuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlobalQuestions::route('/'),
            'create' => CreateGlobalQuestion::route('/create'),
            'edit' => EditGlobalQuestion::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return hexa()->can('question.index');
    }
}
