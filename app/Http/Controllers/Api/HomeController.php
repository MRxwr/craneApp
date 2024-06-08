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

                        //ongoing trip
                        $client_id = $user->id;
               $dt = BookingRequest::with('payment')->where('client_id',$client_id)->where('status', 3)->where('is_deleted', 0)->get();
               $orderRequest =[];
               $ongoingRequest =[];
               $prices=[];
               $key3=0;
                foreach ($dt as $key=>$bookingRequest){
                    $to_lat ='';
                    $to_long='';
                    $from_lat ='';
                    $from_long='';
                    if($bookingRequest->to_latlong){
                        
                        $Tolatlong=explode(',',$bookingRequest->to_latlong);
                        if(count($Tolatlong)==2){
                            $to_lat = $Tolatlong[0];
                            $to_long = $Tolatlong[1];
                        }
                    }
                    if($bookingRequest->from_latlong){
                        $Fromlatlong=explode(',',$bookingRequest->from_latlong);
                        if(count($Fromlatlong)==2){
                            $from_lat = $Fromlatlong[0];
                            $from_long = $Fromlatlong[1];
                        }
                    }
                    if($bookingRequest->status==3){
                        $ongoingRequest[$key3]['bidid']=$bookingRequest->id;
                        $ongoingRequest[$key3]['request_id']=$bookingRequest->request_id;
                        $ongoingRequest[$key3]['from_location']=$bookingRequest->from_location;
                        $ongoingRequest[$key3]['to_location']=$bookingRequest->to_location;
                        $ongoingRequest[$key3]['client_name'] = $bookingRequest->client->name;
                        $ongoingRequest[$key3]['client_mobile'] = $bookingRequest->client->mobile;
                        $ongoingRequest[$key3]['status'] = $bookingRequest->status;
                        $ongoingRequest[$key3]['from_lat'] = $from_lat;
                        $ongoingRequest[$key3]['from_lng'] = $from_long;
                        $ongoingRequest[$key3]['to_lat'] = $to_lat;
                        $ongoingRequest[$key3]['to_lng'] = $to_long;
                        $key3++;
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
                $data['ongoingOrders']= [$ongoingRequest];

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
