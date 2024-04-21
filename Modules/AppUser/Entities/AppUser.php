<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUser extends Model
{
    use HasFactory;

    protected $fillable = [
                'name',
                'mobile',
                'email',
                'dob',
                'is_active',
                'user_type',
                'avator'
            ];
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
