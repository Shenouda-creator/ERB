<?php

namespace App\Filament\Admin\Resources\JournalEntries;

use App\Filament\Admin\Resources\JournalEntries\Pages\CreateJournalEntry;
use App\Filament\Admin\Resources\JournalEntries\Pages\EditJournalEntry;
use App\Filament\Admin\Resources\JournalEntries\Pages\ListJournalEntries;
use App\Filament\Admin\Resources\JournalEntries\Schemas\JournalEntryForm;
use App\Filament\Admin\Resources\JournalEntries\Tables\JournalEntriesTable;
use App\Models\JournalEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Icons\Heroicon;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('date')
                ->required(),

            TextInput::make('reference')
                ->maxLength(255),

            Textarea::make('description')
                ->maxLength(500),

            Repeater::make('lines')
                ->relationship()
                ->schema([
                    Select::make('account_id')
                        ->relationship('account', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    TextInput::make('debit')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->live(),

                    TextInput::make('credit')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->live(),
                ])
                ->columns(3)
                ->defaultItems(2)
                ->live()
                ->rule(function () {
                    return function (string $attribute, $value, \Closure $fail) {
                        $debit = collect($value)->sum('debit');
                        $credit = collect($value)->sum('credit');

                        if (round($debit, 2) !== round($credit, 2)) {
                            $fail('القيد غير متوازن: مجموع المدين (' . $debit . ') لازم يساوي مجموع الدائن (' . $credit . ').');
                        }
                    };
                }),

            Placeholder::make('balance_check')
                ->label('Balance Status')
                ->content(function (\Filament\Schemas\Components\Utilities\Get $get) {
                    $lines = $get('lines') ?? [];

                    $debit = collect($lines)->sum(fn($line) => (float) ($line['debit'] ?? 0));
                    $credit = collect($lines)->sum(fn($line) => (float) ($line['credit'] ?? 0));

                    if ($debit == 0 && $credit == 0) {
                        return 'ابدأ بإدخال القيمة';
                    }

                    return $debit == $credit
                        ? "✅ متوازن (مدين: {$debit} = دائن: {$credit})"
                        : "❌ غير متوازن (مدين: {$debit} ≠ دائن: {$credit})";
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('lines_sum_debit')
                    ->label('Total Debit')
                    ->sum('lines', 'debit')
                    ->numeric(2),
                TextColumn::make('lines_sum_credit')
                    ->label('Total Credit')
                    ->sum('lines', 'credit')
                    ->numeric(2),
            ])
            ->defaultSort('date', 'desc')
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
            'index' => ListJournalEntries::route('/'),
            'create' => CreateJournalEntry::route('/create'),
            'edit' => EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
