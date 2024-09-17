<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        // Initialize Firebase Messaging
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

        $this->messaging = $firebase->createMessaging();
    }

    /**
     * Send Firebase Cloud Message to a specific device token
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @return array
     */
    public function sendNotification($deviceToken, $title, $body)
    {
        try {
            // Build the notification message
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ]);

            // Send the message
            $this->messaging->send($message);

            return ['message' => 'Notification sent successfully'];
        } catch (\Exception $e) {
            return ['error' => 'Failed to send notification', 'details' => $e->getMessage()];
        }
    }
}
