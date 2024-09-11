<?php

namespace App\Filament\Widgets;

use App\Models\BookingTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\TableWidget as BaseWidget;

class TopOfficeSpace extends BaseWidget
{
    protected static ?int $sort = 0;
    protected static ?string $heading = 'Top Office';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookingTransaction::query()
                    // ->selectRaw('office_spaces.name,COUNT(booking_transactions.id) AS total')
                    // ->leftJoin('office_spaces','booking_transactions.office_space_id','=','office_spaces.id')
                    // ->where('booking_transactions.is_paid',true)
                    // ->groupBy('office_spaces.name')
                    // ->orderBy('total','desc')
                    // ->get()
                // DB::select("SELECT os.name,COUNT(bt.id) AS total
                // FROM booking_transactions bt
                // left JOIN office_spaces os ON bt.office_space_id = os.id
                // WHERE bt.is_paid IS TRUE
                // GROUP BY os.name
                // ORDER BY total DESC")
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('total')->searchable()->sortable(),
            ]);
    }
}
