<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserActivity extends Model
{
    use HasFactory; 

    protected $table = 'app_user_activities';
    protected $fillable = [
                'app_user_id',
                'request_id',
                'activity',
                'flag',
            ];
}
