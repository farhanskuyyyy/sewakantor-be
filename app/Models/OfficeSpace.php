<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Support\Str;
use App\Models\OfficeSpacePhoto;
use App\Models\OfficeSpaceBenefit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OfficeSpace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'thumbnail',
        'is_open',
        'is_full_booked',
        'price',
        'duration',
        'address',
        'about',
        'slug',
        'city_id',
    ];

    // otomatis
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(OfficeSpacePhoto::class);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(OfficeSpaceBenefit::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function ratings(): BelongsToMany
    {
        // pake ini jg bisa
        // return $this->hasManyThrough(Rating::class, BookingTransaction::class , 'office_space_id','booking_transaction_id','id' ,'id');
        return $this->belongsToMany(Rating::class, 'booking_transactions' , 'office_space_id','id','id' ,'booking_transaction_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
