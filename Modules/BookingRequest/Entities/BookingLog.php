<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AppUser\Entities\AppUser;
use Modules\BookingRequest\Entities\BookingRequest;

class BookingLog extends Model
{
    use HasFactory;

    protected $table = 'booking_logs';
    protected $fillable = [];
    public function client()
    {
        return $this->belongsTo(AppUser::class, 'client_id', 'id');
    }
    public function driver()
    {
        return $this->belongsTo(AppUser::class, 'driver_id', 'id');
    }
    public function requests()
    {
        return $this->belongsTo(BookingRequest::class, 'request_id', 'id');
    }
  
}