<?php

namespace Modules\Service\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
    ];
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];
    
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = json_encode($value);
    }
    public function getTitleAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = json_encode($value);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value, true);
    }
    public function scopeActive($e)
    {
        return $e->where('is_active', 1);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%");
        });
    }
}
