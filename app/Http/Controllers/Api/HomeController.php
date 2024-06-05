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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function getHome(Request $request)
    {
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
              $booking  =  BookingRequest::where('client_id', $user->id)->latest()->first();
              $time =0;
              $distance =0;

               if($booking){
                    $position = DriverPosition::where('request_id', $booking->id)->first();
                        if($position){
                            $time = $position->time;
                            $distance = $position->distance;
                        }
                }
                $services= Service::where('is_active',1)->where('is_deleted',0)
                ->select('id', 'title', 'description','image') ->get()->toArray();
                $banners= Banner::where('is_active',1)->where('is_deleted',0)
                ->select('id', 'title', 'description','image') ->get()->toArray();
                $data['message']=_lang('Get Home Data');
                $data['sevices']= $services;
                $data['banners']= $banners;
                $data['time']= $time;
                $data['distance']= $distance;

                return outputSuccess($data);   
            }else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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
