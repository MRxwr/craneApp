<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverPosition extends Model
{
    use HasFactory;

    protected $table = 'driver_positions';
    protected $fillable = [];

}
