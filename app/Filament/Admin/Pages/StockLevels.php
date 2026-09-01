<?php

namespace App\Filament\Admin\Pages;

use App\Models\Item;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class StockLevels extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Stock Levels';

    protected string $view = 'filament.admin.pages.stock-levels';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Item::query()
                    ->withSum(['movements as stock_in' => fn($query) => $query->where('type', 'in')], 'quantity')
                    ->withSum(['movements as stock_out' => fn($query) => $query->where('type', 'out')], 'quantity')
                    ->orderBy('code')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit'),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->state(function (Item $record): float {
                        $in = (float) ($record->stock_in ?? 0);
                        $out = (float) ($record->stock_out ?? 0);

                        return $in - $out;
                    })
                    ->weight('bold')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('reorder_level')
                    ->label('Reorder Level'),
            ]);
    }
}