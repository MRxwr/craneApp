<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BookingRequest\Entities\BookingRequest;

class BookingLog extends Model
{
    use HasFactory;

    protected $table = 'booking_logs';
    protected $fillable = [];

    public function requests()
    {
        return $this->belongsTo(BookingRequest::class, 'request_prices', 'price_id', 'request_id');
    }
  
}