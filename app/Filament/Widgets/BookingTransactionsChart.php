<?php

namespace App\Filament\Widgets;

use App\Models\BookingTransaction;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class BookingTransactionsChart extends ChartWidget
{
    protected static ?string $heading = 'Booking Chart';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = $this->getBookingTransactionsPerMonth();
        return [
            'datasets' => [
                [
                    'label' => 'Booking',
                    'data' => $data['perMonth']
                ]
            ],
            'labels' => $data['months']
        ];
    }

    public function getBookingTransactionsPerMonth() {
        $now = Carbon::now();
        $perMonth = [];

        $months = collect(range(1,12))->map(function($month) use($now,&$perMonth){
            $count = BookingTransaction::whereMonth('created_at',Carbon::parse($now->month($month)->format('Y-m')))->where('is_paid',true)->count();

            $perMonth[] = $count;
            return $now->month($month)->format('m');
        })->toArray();
        return [
            'perMonth' => $perMonth,
            'months' => $months
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
