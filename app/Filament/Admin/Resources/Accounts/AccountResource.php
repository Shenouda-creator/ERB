<?php

namespace App\Filament\Admin\Resources\Accounts;

use App\Filament\Admin\Resources\Accounts\Pages\CreateAccount;
use App\Filament\Admin\Resources\Accounts\Pages\EditAccount;
use App\Filament\Admin\Resources\Accounts\Pages\ListAccounts;
use App\Filament\Admin\Resources\Accounts\Schemas\AccountForm;
use App\Filament\Admin\Resources\Accounts\Tables\AccountsTable;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions;
class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

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

            Select::make('type')
                ->options([
                    'asset' => 'Asset (أصول)',
                    'liability' => 'Liability (خصوم)',
                    'equity' => 'Equity (حقوق ملكية)',
                    'revenue' => 'Revenue (إيرادات)',
                    'expense' => 'Expense (مصروفات)',
                ])
                ->required(),

            Select::make('parent_id')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->label('Parent Account'),

            Toggle::make('is_active')
                ->default(true),
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
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('parent.name')
                    ->label('Parent'),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
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
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}
