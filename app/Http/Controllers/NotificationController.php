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
        try {
            $user_id=$request->id;
            $title=_lang('new trip');
            $message=_lang('Client create New trip please Bid');
            $status =  firebaseNotification($user_id,$title,$message='',$data=[]);
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
