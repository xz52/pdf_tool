<?php 

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionTypesEnum: string implements HasLabel
{
    case CHOOSE = 'choose';
    case T_F = 'T_F';

    public function getLabel(): string
    {
        return match ($this) {
            self::CHOOSE => 'Choose',
            self::T_F => 'True/False',
        };
    }
}