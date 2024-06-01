<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserRating extends Model
{
    use HasFactory;

    protected $table = 'app_user_ratings';
    protected $fillable = [
                'app_user_id', //who given rating 
                'rating_user_id', // who taken rating
                'rating',
                'remarks',
            ];
}
