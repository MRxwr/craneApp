<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallets extends Model
{
    use HasFactory;
    protected $table = 'wallets';
    protected $fillable = [];
  
}
