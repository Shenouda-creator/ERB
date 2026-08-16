<?php

namespace App\Filament\Admin\Resources\Shifts;

use App\Filament\Admin\Resources\Shifts\Pages\CreateShift;
use App\Filament\Admin\Resources\Shifts\Pages\EditShift;
use App\Filament\Admin\Resources\Shifts\Pages\ListShifts;
use App\Filament\Admin\Resources\Shifts\Pages\ViewShift;
use App\Filament\Admin\Resources\Shifts\Schemas\ShiftForm;
use App\Filament\Admin\Resources\Shifts\Schemas\ShiftInfolist;
use App\Filament\Admin\Resources\Shifts\Tables\ShiftsTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use App\Models\Shift;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Filament\Actions\Actions;


class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TimePicker::make('start_time')
                ->required(),

            TimePicker::make('end_time')
                ->required(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShiftInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->time(),
                TextColumn::make('end_time')
                    ->time(),
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
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'view' => ViewShift::route('/{record}'),
            'edit' => EditShift::route('/{record}/edit'),
        ];
    }
}
