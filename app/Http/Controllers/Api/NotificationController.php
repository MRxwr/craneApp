<?php

namespace App\Http\Controllers\Api;

use Modules\Service\Entities\Service;
use Modules\Banners\Entities\Banner;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\DriverPosition;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\AppUserActivity;
use App\Models\Setting;
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
        // Validate the input
        $request->validate([
            'to_user_id' => 'integer',
            'title' => 'required|string',
            'message' => 'required|string',
            'bidid' => 'nullable|string',
            'clienetId' => 'nullable|string',
            'driverid' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        // Get the device token, title, body, and extra parameters
        $to_user_id = $request->input('to_user_id');
        $title = $request->input('title');
        $body = $request->input('message');
        // Additional custom data
        $data = [
            'bidid' => $request->input('bidid'),
            'clienetId' => $request->input('clienetId'),
            'driverid' => $request->input('driverid'),
            'image' => $request->input('image'),
        ];
        try {
            $user_id=$to_user_id;
            $message=$body;
            $status =  firebaseNotification($user_id,$title,$body,$data);
            $data['status']=$status;
            return outputSuccess($data);
        } catch (\Exception $e) {
            $data['message']=_lang('Authentication error');
            $data['errors'] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            return outputError($data);
        }
    }
}
