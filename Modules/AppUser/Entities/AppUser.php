<?php

namespace Modules\AppUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Hash;

class AppUser extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;
    protected $table = 'app_users';
    protected $fillable = [
                'name',
                'mobile',
                'password',
                'email',
                'dob',
                'is_active',
                'user_type',
                'avator'
            ];
    // The attributes that should be hidden for arrays
    protected $hidden = [
        'password',
    ];        
    public function scopeActive($e)
    {
        return $e->where('is_active', 1);
    }
    public function scopeFilter($e, $q)
    {
        return $e->when($q, function ($ee, $q) {
            return $ee->where('name', 'like', "%$q%")
                ->orWhere('mobile', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%");
        });
    }
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'mobile';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Validate the user's password.
     *
     * @param  string  $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Hash::check($password, $this->getAuthPassword());
    }
    
}
