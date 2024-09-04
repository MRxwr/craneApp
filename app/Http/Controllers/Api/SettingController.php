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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SettingController extends Controller
{
    public function getSetting(Request $request)
    {
        $data = array();
        try {
            $setting = Setting::where('id',1)->first();
            if($request->input('action')=='version'){
                if($request->input('type')=='list'){
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }else if($request->input('type')=='update'){

                    if($request->input('ios')){
                        $setting->ios_version = $request->input('ios');
                    }
                    if($request->input('android')){
                        $setting->android_version = $request->input('android');
                    }
                    // if($request->input('ios')){
                    //     $setting->ios_app_link = $request->input('ios_link');
                    // }
                    // if($request->input('ios')){
                    //     $setting->android_app_link = $request->input('android_link');
                    // }
                    $setting->save();
                    
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }else{
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }
        }else if($request->input('action')=='link'){
            if($request->input('type')=='list'){
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 

            }else if($request->input('type')=='update'){

                if($request->input('ios_link')){
                    $setting->ios_app_link = $request->input('ios_link');
                }
                if($request->input('android_link')){
                    $setting->android_app_link = $request->input('android_link');
                }
                $setting->save();
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 
            }else{
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 
            }
        }
        } catch (\Exception $e) {
            $data['message']=_lang('Api error');
            $data['errors'] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            return outputError($data); 
        }
        
    }
    public function testFirebaseNotification(Request $request){

        if($request->id){
            try{
                $user_id=$request->id;
                $title=_lang('new trip');
                $message=_lang('Client create New trip please Bid');
                $status =  firebaseNotification($user_id,$title,$message='',$data=[]);
                $data['status']=$status;
                return outputSuccess($data); 
            }catch (\Exception $e) {
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

    public function CronForCompleteTrip(Request $request){
        $threeHoursAgo = Carbon::now()->subHours(3);
        $bookingRequests = BookingRequest::where('status', 5)->where('notify', 0)->where('updated_at', '<', $threeHoursAgo)->get();
        if(bookingRequests){
            try{
                foreach($bookingRequests as $bookingRequest){
                    if($bookingRequest->client_id>0){
                        $user_id=bookingRequest->client_id;
                        $title=_lang('Trip Completed');
                        $message=_lang('Your driver is waiting for your feedback. You can rate him now.');
                        $status =  firebaseNotification($user_id,$title,$message='',$data=[]);  
                    }
                    if($bookingRequest->driver_id>0){
                        $user_id=bookingRequest->driver_id;
                        $title=_lang('Trip Completed');
                        $message=_lang('Please rate your last trip client.');
                        $status =  firebaseNotification($user_id,$title,$message='',$data=[]);  
                    }  
                    $bookingRequest->notify = 1;
                    $bookingRequest->save();
                }   
            }catch (\Exception $e) {
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
}
