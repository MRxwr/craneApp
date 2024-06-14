<?php

namespace Modules\Notifications\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Notifications\Entities\Notification;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\AppUser\Entities\AppUser;
use Illuminate\Routing\Controller;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getNotifications(Request $request)
    {
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->first();
            if ($user) {
                $notifications= Notification::where('app_user_id', $user->id)->where('is_deleted',0)->get()->toArray();
                $data['message']=_lang('Get Notifications');
                $data['notifications']= $notifications;
                return outputSuccess($data); 
            }{
                $data['message']=_lang('User not exist with this token');
                return outputError($data); 
            }
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

    public function saveUserNotification(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->first();
            if ($user) {
                $data['message']=_lang('Send Crane Request');
                $dt = new Notification;
                $dt->client_id = $request->input('client_id');
                $dt->driver_id = $request->input('driver_id');
                $dt->NotificationReciver =  $request->input('NotificationReciver');
                $dt->message =  $request->input('message');
                $dt->read = 0;
                if($dt->save()){
                     $data['message']=_lang('Successfully added your notification');
                     return outputSuccess($data);
                 } 
            }else{
                $data['message']=_lang('User not exist with this token');
                return outputError($data); 
            }
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
