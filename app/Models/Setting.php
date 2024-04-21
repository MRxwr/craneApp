<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'sitetitle',
        'sitedesc',
        'adminlang',
        'frontlang',
        'logo',
        'favicon'
    ];
    
    public function setSitetitleAttribute($value)
    {
        $this->attributes['sitetitle'] = json_encode($value);
    }
    public function getSitetitleAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSitedescAttribute($value)
    {
        $this->attributes['sitedesc'] = json_encode($value);
    }
    public function getSitedescAttribute($value)
    {
        return json_decode($value, true);
    }
}
