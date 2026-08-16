<?php

namespace App\Filament\Admin\Resources\LeaveRequests;

use App\Filament\Admin\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Admin\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Admin\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;


class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'exit';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->relationship('employee', 'name')
                ->required()
                ->searchable()
                ->preload(),

            DatePicker::make('start_date')
                ->required(),

            DatePicker::make('end_date')
                ->required(),

            TextInput::make('reason')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(LeaveRequest $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update(['status' => 'approved']);
                    }),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(LeaveRequest $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update(['status' => 'rejected']);
                    }),

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
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
