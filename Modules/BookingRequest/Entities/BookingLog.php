<?php

namespace Modules\BookingRequest\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingLog extends Model
{
    use HasFactory;

    protected $table = 'booking_logs';
    protected $fillable = [];
  
