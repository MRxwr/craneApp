<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\AuthException;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(config('firebase.credentials.file'));

            $this->auth = $firebase->createAuth();
        } catch (\Exception $e) {
            // Log or handle initialization errors
            throw new \RuntimeException('Failed to initialize Firebase Auth: ' . $e->getMessage());
        }
    }

    public function getBearerToken()
    {
        try {
            $customToken = $this->auth->createCustomToken('user-uid');
            $signInResult = $this->auth->signInWithCustomToken($customToken);
            $idToken = $signInResult->idToken();

            return $idToken;
        } catch (AuthException $e) {
            // Log or handle Firebase Auth specific exceptions
            return 'Error: ' . $e->getMessage();
        } catch (\Exception $e) {
            // Log or handle general exceptions
            return 'Error: ' . $e->getMessage();
        }
    }
}
