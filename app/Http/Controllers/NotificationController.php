<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        // Initialize Firebase Messaging
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification(Request $request)
    {
        $deviceToken = $request->input('device_token'); // FCM device token

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => 'Hello!',
                'body' => 'This is a Firebase Cloud Message!',
            ]);

        try {
            $this->messaging->send($message);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send notification', 'details' => $e->getMessage()], 500);
        }
    }
}
