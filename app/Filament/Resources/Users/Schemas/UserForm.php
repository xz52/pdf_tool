<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),

                Select::make('roles')
                    ->label(__('Role Name'))
                    ->relationship('roles', 'name')
                    ->placeholder(__('Superuser')),

                TextInput::make('password')
                    ->password()
                    ->rule(Password::default())
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)),
            ]);
    }
}
