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
            ->withServiceAccount("/home/u341645071/domains/trycraneapp.com/public_html/admincp/storage/app/fcm/crane-dbb8b-firebase-adminsdk-uezkp-e824b13302.json");

       $this->firebaseAuth = $firebase->createAuth();
    }

    // Function to verify Firebase ID Token
    public function getBearerToken()
    {
        dd(config('firebase.credentials.file'));
         // Example: Authenticate a user and get an ID token (Bearer token)
         $email = 'Createkwco@gmail.com'; // Replace with your Firebase user's email
         $password = 'N@b$90949089'; // Replace with the user's password
         
         // Sign in the user with email and password
         $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($email, $password);
         dd($signInResult);
         // Get the Bearer Token (Firebase ID Token)
         $idToken = $signInResult->idToken();  // This is the Bearer Token
        
         return response()->json(['token' => $idToken]);
    }
    
}