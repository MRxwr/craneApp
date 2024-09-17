<?php 
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));  // Path to your service account JSON
        
        $this->auth = $firebase->createAuth();
    }

    /**
     * Get a Bearer Token using the Firebase service account credentials.
     *
     * @return string|null
     */
    public function getBearerToken()
    {
        try {
            // Create a custom token for a specific user, if needed
            $customToken = $this->auth->createCustomToken('user-uid');

            // Exchange the custom token for an ID token (Bearer token)
            $signInResult = $this->auth->signInWithCustomToken($customToken);

            // Extract the ID token (Bearer token) from the sign-in result
            $idToken = $signInResult->idToken();

            return $idToken;  // Bearer token to use for authorization
        } catch (InvalidToken $e) {
            // Token is invalid
            return null;
        } catch (FirebaseException $e) {
            // Firebase-specific exceptions
            return null;
        }
    }
}
