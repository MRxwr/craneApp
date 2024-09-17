<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Import the correct Controller class
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseAuthController extends Controller
{
    protected $firebaseAuth;
    public function __construct()
    {
       // Initialize Firebase with the service account
       $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

       $this->firebaseAuth = $firebase->createAuth();
    }

    // Function to verify Firebase ID Token
    public function getBearerToken()
    {
        try {
            // Example: Authenticate a user and get an ID token (Bearer token)
            $email = 'Createkwco@gmail.com'; // Replace with your Firebase user's email
            $password = 'N@b$90949089'; // Replace with the user's password
            
            // Sign in the user with email and password
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($email, $password);
            
            // Get the Bearer Token (Firebase ID Token)
            $idToken = $signInResult->idToken();  // This is the Bearer Token
            
            return response()->json(['token' => $idToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to get token', 'details' => $e->getMessage()], 500);
        }
    }
    
}
