<?php

namespace App\Filament\Admin\Resources\CostCenters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CostCenterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
