<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $fillable = [
        'flag',
        'code',
        'title',
        'iso_code',
        'status'
    ];
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('code', 'like', "%$q%")
                ->orWhere('title', 'like', "%$q%");
        });
    }
}
