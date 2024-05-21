<?php

namespace Modules\Coupons\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'coupon_code',
        'coupon_type',
        'coupon_value',
        'expiry_date',  
    ];
    protected $casts = [
        'title' => 'array',
    ];
    
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = json_encode($value);
    }
    public function getTitleAttribute($value)
    {
        return json_decode($value, true);
    }

    public function scopeActive($e)
    {
        return $e->where('is_active', 1);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%");
        });
    }
    public function isExpired()
    {
        return $this->expiry_date < now();
    }
}
