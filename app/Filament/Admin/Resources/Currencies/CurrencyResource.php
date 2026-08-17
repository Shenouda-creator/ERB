<?php

namespace App\Filament\Admin\Resources\Currencies;

use App\Filament\Admin\Resources\Currencies\Pages\CreateCurrency;
use App\Filament\Admin\Resources\Currencies\Pages\EditCurrency;
use App\Filament\Admin\Resources\Currencies\Pages\ListCurrencies;
use App\Filament\Admin\Resources\Currencies\Schemas\CurrencyForm;
use App\Filament\Admin\Resources\Currencies\Tables\CurrenciesTable;
use App\Models\Currency;
use Filament\Forms\Components\TextInput;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->required()
                ->maxLength(3)
                ->dehydrateStateUsing(fn(string $state): string => strtoupper($state)),
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('symbol')
                ->maxLength(5),

            TextInput::make('exchange_rate')
                ->required()
                ->numeric()
                ->default(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('symbol'),
                TextColumn::make('exchange_rate')
                    ->numeric(decimalPlaces: 4),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }
}
