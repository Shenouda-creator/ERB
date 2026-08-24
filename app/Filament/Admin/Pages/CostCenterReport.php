<?php

namespace App\Filament\Admin\Pages;

use App\Models\CostCenter;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class CostCenterReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Cost Center Report';

    protected string $view = 'filament.admin.pages.cost-center-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(CostCenter::query()->orderBy('code'))
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Cost Center'),
                Tables\Columns\TextColumn::make('total_debit')
                    ->label('Total Debit')
                    ->state(fn (CostCenter $record): float => $record->lines()->sum('debit'))
                    ->numeric(2),
                Tables\Columns\TextColumn::make('total_credit')
                    ->label('Total Credit')
                    ->state(fn (CostCenter $record): float => $record->lines()->sum('credit'))
                    ->numeric(2),
                Tables\Columns\TextColumn::make('net_cost')
                    ->label('Net Cost')
                    ->state(fn (CostCenter $record): float => $record->lines()->sum('debit') - $record->lines()->sum('credit'))
                    ->weight('bold')
                    ->numeric(2),
            ]);
    }
}