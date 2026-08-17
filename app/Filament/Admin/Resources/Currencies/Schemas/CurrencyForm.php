<?php

namespace App\Filament\Admin\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('symbol')
                    ->default(null),
                TextInput::make('exchange_rate')
                    ->required()
                    ->numeric()
                    ->default(1.0),
            ]);
    }
}
