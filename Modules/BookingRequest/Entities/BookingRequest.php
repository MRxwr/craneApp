<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AppUser\Entities\AppUser;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\BookingLog;


class BookingRequest extends Model
{
    protected $table = 'booking_requests';
    protected $fillable = [];
    // The attributes that should be hidden for arrays        
    public function scopeActive($e)
    {
        return $e->where('is_active', 1);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('name', 'like', "%$q%")
                ->orWhere('mobile', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%");
        });

    }
    public function client()
    {
        return $this->belongsTo(AppUser::class, 'client_id', 'id');
    }
    // public function driver()
    // {
    //     return $this->belongsTo(AppUser::class, 'driver_id', 'id');
    // }

    public function prices()
    {
        return $this->hasMany(BookingPrice::class, 'request_id', 'id');
    }
    public function logs()
    {
        return $this->hasMany(BookingLog::class, 'request_id', 'id');
    }
    public function payment() {
        return $this->hasOne(BookingPayment::class, 'request_id');
    }
}
