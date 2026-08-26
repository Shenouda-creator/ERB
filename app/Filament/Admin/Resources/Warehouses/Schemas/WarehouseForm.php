<?php

namespace App\Filament\Admin\Resources\Warehouses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('branch_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('location')
                    ->default(null),
            ]);
    }
}
