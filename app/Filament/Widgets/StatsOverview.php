<?php

namespace App\Filament\Widgets;

use App\Models\OfficeSpace;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // $reports = DB::select("SELECT
        //                         DATE_FORMAT(created_at, '%Y-%m') AS bulan,
        //                         COUNT(*) AS total_transaksi,
        //                     COUNT(CASE WHEN is_paid = true THEN 1 END) AS success,
        //                     COUNT(CASE WHEN is_paid = false THEN 1 END) AS pending FROM booking_transactions
        //                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        //                     GROUP BY bulan
        //                     ORDER BY bulan",
        //     []);

        return [
            Stat::make('Total Office', OfficeSpace::count()),
                // ->description("Increase in offices")
                // ->descriptionIcon("heroicon-m-arrow-trending-up")
                // ->color("success")
                // ->chart([2, 3, 1, 2, 3, 2, 1, 2]),

            Stat::make('Pending Booking', BookingTransaction::where('is_paid', false)->count()),
                // ->description("Total pending booking in app")
                // ->descriptionIcon("heroicon-m-arrow-trending-down")
                // ->color("danger"),
                // ->chart([2, 3, 4, 2, 1, 0, 1, 2]),

            Stat::make('Total Success Booking', BookingTransaction::where('is_paid', true)->count()),
                // ->description("Total orders in app")
                // ->descriptionIcon("heroicon-m-arrow-trending-down")
                // ->color("danger")
                // ->chart([2, 3, 4, 2, 5, 0, 3, 2]),
        ];
    }
}
