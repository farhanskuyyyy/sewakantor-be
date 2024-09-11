<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingTransactionResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBooking extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Latest Booking';

    public function table(Table $table): Table
    {
        return $table
            ->query(BookingTransactionResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('booking_trx_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('officeSpace.name')->searchable()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_paid')->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->label('Payment Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Order Date')->date()->sortable(),
            ]);
    }
}
