<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpUser extends Model
{
    use HasFactory;

    protected $fillable = ['otp','mobile'];
    
    
}
