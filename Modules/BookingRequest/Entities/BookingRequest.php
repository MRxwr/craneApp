<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
