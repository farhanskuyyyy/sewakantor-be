<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_paid',true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('booking_trx_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->prefix('Days'),
                DatePicker::make('started_at')
                    ->required(),
                DatePicker::make('ended_at')
                    ->required(),
                Toggle::make('is_paid')
                    ->label("Paid")
                    ->helperText('Payment Status')
                    ->default(true),
                Select::make('office_space_id')
                    ->relationship('officeSpace', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('booking_trx_id')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('officeSpace.name')->searchable(),
                TextColumn::make('started_at')->searchable()->date(),
                IconColumn::make('is_paid')->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->label('Payment Status')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
