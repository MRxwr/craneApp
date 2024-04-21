<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'locales',
    ];

    protected $casts = [
        'locales' => 'array',
    ];
    public function setLocalesAttribute($value)
    {
        $this->attributes['locales'] = json_encode($value);
    }
    public function getLocalesAttribute($value)
    {
        return json_decode($value, true);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('slug', 'like', "%$q%")
                ->orWhere('locales', 'like', "%$q%");
        });
    }
}
