<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';
    protected $fillable = [
                'app_user_id',
                'start_time',
                'end_time',
            ];
    
}
