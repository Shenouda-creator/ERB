<?php

namespace App\Filament\Admin\Resources\PurchaseOrders;

use App\Filament\Admin\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Admin\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Admin\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\StockMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('warehouse_id')
                ->relationship('warehouse', 'name')
                ->required()
                ->searchable()
                ->preload(),

            DatePicker::make('order_date')
                ->required()
                ->default(now()),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'received' => 'Received',
                ])
                ->required()
                ->default('pending')
                ->disabled(fn(string $operation): bool => $operation === 'create'),

            Repeater::make('items')
                ->relationship()
                ->schema([
                    Select::make('item_id')
                        ->relationship('item', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->required(),

                    TextInput::make('unit_price')
                        ->numeric()
                        ->required(),
                ])
                ->columns(3)
                ->defaultItems(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse'),
                TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'received' => 'success',
                    }),
            ])
            ->defaultSort('order_date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Actions\Action::make('receive')
                    ->label('Mark as Received')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(PurchaseOrder $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record): void {
                        foreach ($record->items as $orderItem) {
                            StockMovement::create([
                                'company_id' => $record->company_id,
                                'warehouse_id' => $record->warehouse_id,
                                'item_id' => $orderItem->item_id,
                                'quantity' => $orderItem->quantity,
                                'type' => 'in',
                                'reference' => 'Purchase Order #' . $record->id,
                                'date' => now(),
                            ]);
                        }

                        $record->update(['status' => 'received']);
                    }),

                Actions\EditAction::make(),
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
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
