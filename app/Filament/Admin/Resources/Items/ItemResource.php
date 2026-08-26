<?php

namespace App\Filament\Admin\Resources\Items;

use App\Filament\Admin\Resources\Items\Pages\CreateItem;
use App\Filament\Admin\Resources\Items\Pages\EditItem;
use App\Filament\Admin\Resources\Items\Pages\ListItems;
use App\Filament\Admin\Resources\Items\Tables\ItemsTable;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->required()
                ->maxLength(255),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->label('Unit'),

            TextInput::make('reorder_level')
                ->numeric()
                ->default(0)
                ->label('Reorder Level'),
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
                TextColumn::make('unit.name')
                    ->label('Unit'),
                TextColumn::make('reorder_level')
                    ->label('Reorder Level'),
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
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }
}
