<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'rate',
        'comment',
        'booking_transaction_id',
    ];

    public function bookingTransaction(): BelongsTo
    {
        return $this->belongsTo(BookingTransaction::class,'booking_transaction_id');
    }

}
