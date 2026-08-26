<?php

namespace App\Filament\Admin\Resources\Warehouses;

use App\Filament\Admin\Resources\Warehouses\Pages\CreateWarehouse;
use App\Filament\Admin\Resources\Warehouses\Pages\EditWarehouse;
use App\Filament\Admin\Resources\Warehouses\Pages\ListWarehouses;
use App\Models\Warehouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('branch_id')
                ->relationship('branch', 'name')
                ->searchable()
                ->preload()
                ->label('Branch'),

            TextInput::make('location')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('branch.name')
                    ->label('Branch'),
                TextColumn::make('location'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'edit' => EditWarehouse::route('/{record}/edit'),
        ];
    }
}
