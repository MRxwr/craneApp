<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;


class FCMService
{
    protected $serverKey;
    protected $senderId;

    public function __construct()
    {
        $this->serverKey = env('FCM_SERVER_KEY');
        $this->senderId = env('FCM_SENDER_ID');
    }

    public static function sendNotification($token, $title, $body, $data = [])
    {
        $serverKey = env('FCM_SERVER_KEY');
        $senderId = env('FCM_SENDER_ID');
        $url = 'https://fcm.googleapis.com/fcm/send';
        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => array_merge($data, ['sender_id' => $senderId]),
        ];
        $headers = [
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ];
        $response = Http::withHeaders($headers)->post($url, $payload);
        return $response->json();
    }
}