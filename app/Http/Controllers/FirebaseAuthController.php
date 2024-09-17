<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseAuthController extends Controller
{
    protected $auth;

    public function __construct()
    {
        // Initialize Firebase
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

        $this->auth = $firebase->createAuth();
    }

    // Function to verify Firebase ID Token
    public function verifyToken(Request $request)
    {
        $idTokenString = $request->input('idToken');

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->claims()->get('sub');

            $user = $this->auth->getUser($uid);
            return response()->json(['message' => 'Token verified', 'user' => $user]);

        } catch (\Kreait\Firebase\Exception\Auth\InvalidToken $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => 'Malformed token'], 400);
        }
    }
    
}
