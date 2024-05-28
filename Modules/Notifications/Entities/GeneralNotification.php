<?php

namespace Modules\Notifications\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralNotification extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    public function scopeActive($e)
    {
        //return $e->where('is_read', 1);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('title', 'like', "%$q%")
                ->orWhere('text', 'like', "%$q%");
        });
    }
}
