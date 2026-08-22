<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class GeneralLedger extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'General Ledger';

    protected string $view = 'filament.admin.pages.general-ledger';

    public function table(Table $table): Table
    {
        return $table
            ->query(Account::query()->orderBy('code'))
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Account'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_debit')
                    ->label('Total Debit')
                    ->state(fn(Account $record): float => $record->lines()->sum('debit'))
                    ->numeric(2),
                Tables\Columns\TextColumn::make('total_credit')
                    ->label('Total Credit')
                    ->state(fn(Account $record): float => $record->lines()->sum('credit'))
                    ->numeric(2),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->state(function (Account $record): float {
                        $debit = $record->lines()->sum('debit');
                        $credit = $record->lines()->sum('credit');

                        return in_array($record->type, ['asset', 'expense'])
                            ? $debit - $credit
                            : $credit - $debit;
                    })
                    ->weight('bold')
                    ->numeric(2),
            ]);
    }
}