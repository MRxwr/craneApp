<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingPrice extends Model
{
    use HasFactory;

    protected $table = 'booking_prices';
    protected $fillable = [];
  
    
}
