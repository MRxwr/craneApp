<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserMeta extends Model
{
    use HasFactory;


    protected $table = 'app_user_metas';
    protected $fillable = [
                'app_user_id',
                'key',
                'value',
            ];
    
}
