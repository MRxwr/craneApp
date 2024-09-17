<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;

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
}
