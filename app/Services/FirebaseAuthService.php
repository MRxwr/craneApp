<?php 
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseAuthService
{
    protected $auth;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

        $this->auth = $firebase->createAuth();
    }

    /**
     * Get Firebase token for a user
     * 
     * @param string $email
     * @param string $password
     * @return array
     */
    public function getFirebaseToken($email, $password)
    {
        try {
            // Sign in with email and password
            $signInResult = $this->auth->signInWithEmailAndPassword($email, $password);
            
            // Get the Firebase ID token
            $idToken = $signInResult->idToken();
            
            return [
                'success' => true,
                'token' => $idToken,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
