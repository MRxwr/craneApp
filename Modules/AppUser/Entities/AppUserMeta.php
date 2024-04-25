<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserMeta extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\AppUser\Database\factories\AppUserMetaFactory::new();
    }
}
