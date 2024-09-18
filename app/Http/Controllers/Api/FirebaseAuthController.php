<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Google\Client;


class FirebaseAuthController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get the Bearer Token and return it as JSON response.
     *
     * @return JsonResponse
     */
    public function getBearerToken()
    {
        $token = $this->firebaseService->getBearerToken();

        if ($token) {
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unable to get token'], 500);
        }
    }
   public function getAccessToken() {
        // Path to your service account key
        $serviceAccountKeyFile = config('firebase.credentials.file');
    
        // Initialize the Google API Client
        $client = new Client();
        $client->setAuthConfig($serviceAccountKeyFile);
    
        // Define the scopes you need (for FCM, you need cloud messaging scope)
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    
        // Fetch the access token
        $token = $client->fetchAccessTokenWithAssertion();
    
        //return $token['access_token'];
        if ($token) {
            return response()->json(['token' => $token['access_token']], 200);
        } else {
            return response()->json(['error' => 'Unable to get token'], 500);
        }
    }
    
}
