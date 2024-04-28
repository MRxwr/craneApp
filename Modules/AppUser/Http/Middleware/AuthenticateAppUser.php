<?php
namespace Modules\AppUser\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateAppUser extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('api')->check()) {
            return $this->auth->shouldUse('api');
        }
        parent::authenticate($request, $guards);
    }
}