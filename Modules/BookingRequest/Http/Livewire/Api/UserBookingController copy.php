<?php
namespace Modules\BookingRequest\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\BookingPayment;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\LoginAttempt;
use Modules\AppUser\Entities\AppUserActivity;
use Illuminate\Routing\Controller;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;
use Carbon\Carbon;


class UserBookingController extends Controller
{
    public function GetClientHome(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $clientId = $user->id;
               $data['message']=_lang('get Order request');
               $dt = BookingRequest::with('prices')->with('payment')->where('client_id', $user->id)->where('is_deleted', 0)->whereIn('status',['',0,1,2,3,4,5])->get();
               $orderRequest =[];
               $prices=[];
                foreach ($dt as $key=>$bookingRequest){
                        $createdAt = $bookingRequest->created_at;
                        $hour = $createdAt->format('g');
                        $hour = $hour < 10 ? '100' : $hour;
                        // Format the rest
                        $formattedDate = $createdAt->format('dM ') . $hour . $createdAt->format(':iA');
                        $payment = BookingPayment::where('request_id', $bookingRequest->id)->first();
                        if($payment){
                            $prices=   BookingPrice::where('request_id', $bookingRequest->id)->where('driver_id', $payment->driver_id)->first();
                        }
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
                        $client_rating = getUserRating($bookingRequest->client_id);
                        $orderRequest[$key]['bidid']=$bookingRequest->id;
                        $orderRequest[$key]['request_id']=$bookingRequest->request_id;
                        $orderRequest[$key]['from_location']=$bookingRequest->from_location;
                        $orderRequest[$key]['to_location']=$bookingRequest->to_location;
                        $orderRequest[$key]['client_id'] = $bookingRequest->client->id;
                        $orderRequest[$key]['client_name'] = $bookingRequest->client->name;
                        $orderRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                        $orderRequest[$key]['client_avator'] = $bookingRequest->client->avator;
                        $orderRequest[$key]['client_rating'] = $client_rating;
                        
                        if($bookingRequest->driver_id>0){
                            $driver_rating = getUserRating($bookingRequest->driver->id);
                            $orderRequest[$key]['driver_id'] = $bookingRequest->driver->id;
                            $orderRequest[$key]['driver_name'] = $bookingRequest->driver->name;
                            $orderRequest[$key]['driver_mobile'] = $bookingRequest->driver->mobile;
                            $orderRequest[$key]['driver_avator'] = $bookingRequest->driver->avator; 
                            $orderRequest[$key]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                            $orderRequest[$key]['driver_rating'] = $driver_rating;
                        }
                        $orderRequest[$key]['status'] = $bookingRequest->status;
                        $orderRequest[$key]['from_lat'] = $from_lat;
                        $orderRequest[$key]['from_lng'] = $from_long;
                        $orderRequest[$key]['to_lat'] = $to_lat;
                        $orderRequest[$key]['to_lng'] = $to_long;
                        $orderRequest[$key]['time'] = $formattedDate;
                       if($payment){
                         $orderRequest[$key]['payment_status'] = $payment->payment_status; 
                         $orderRequest[$key]['bid_amount'] = $payment->payment_amount; 
                         $orderRequest[$key]['coupon_discount'] = $payment->coupon_discount; 
                         $orderRequest[$key]['coupon_code'] = $payment->coupon_code; 
                         $orderRequest[$key]['trip_cost'] = $payment->payment_amount; 
                       }
                       $orderRequest[$key]['trip_start'] = $bookingRequest->start_time;
                       $orderRequest[$key]['trip_end'] = $bookingRequest->end_time;
                        
                  }
                
                $data['ClientOrder']= $orderRequest;
               return outputSuccess($data);
                // Proceed with authenticated user logic
                
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized');
                return outputError($data); 
                
            }
        } catch (\Exception $e) {
            // Log or handle the exception
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
    public function GetDriverHome(Request $request){
        $data = array();
        $today = Carbon::today();
         $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
          $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            //var_dump($user);
            if ($user) {
                /// Retrieve all completed login attempts for the user
                $completedLoginAttempts = LoginAttempt::where('app_user_id', $user->id)
                    ->whereNotNull('end_time')
                    ->get();
                $totalLoginTime = 0;
                // Iterate through each login attempt and calculate the duration
                foreach ($completedLoginAttempts as $attempt) {
                    $startTime = Carbon::parse($attempt->start_time);
                    $endTime = Carbon::parse($attempt->end_time);
                    // Calculate the difference in seconds and add to total
                    $totalLoginTime += $endTime->diffInSeconds($startTime);
                }
                // Convert total login time to a more readable format (e.g., hours, minutes, seconds)
                $totalLoginTimeFormatted = gmdate('H:i:s', $totalLoginTime);

                $driverId = $user->id;
               $data['message']=_lang('get Order request');
               $todayRequests = BookingRequest::where('is_deleted', 0)
            ->whereHas('payment', function($query) use ($today, $driverId) {
                $query->whereDate('created_at', $today)
                    ->where('driver_id', $driverId);
            })
            ->with(['payment' => function($query) use ($today, $driverId) {
                $query->whereDate('created_at', $today)
                    ->where('driver_id', $driverId);
            }])
            ->get();
            $totalEarnings = $todayRequests->sum(function($bookingRequest) {
                return @$bookingRequest->payment->payment_amount;
            });
            $totalDistance = $todayRequests->sum('distances'); // Assuming 'distance' is a field in BookingRequest
            $totalRequests = $todayRequests->count();
               $data['todayEarnings']= [
                'total_earnings' => $totalEarnings,
                'total_distance' => $totalDistance,
                'total_trips' => $totalRequests,
                'time_online'=>$totalLoginTimeFormatted,
                'login_status' => AppUserLogingStatus($driverId)
            ];

            
            $dtnew = BookingRequest::with(['prices' => function($query) use ($user) {
                $query->where('driver_id', $user->id)
                      ->whereNull('price')// Use single '=' for the condition
                      ->where('is_accepted', '0')
                      ->where('skip', '0');
            }])->where('status', '0')
              ->where('is_deleted', '0')
              ->orderBy('created_at', 'desc')
              ->get();
             $newTripRequest =[];
             
             $prices=[];
             $nkey=0;
            
             foreach($dtnew as $key=>$bookingRequest) {
                    $lat ='';
                    $long='';
                    if($bookingRequest->to_latlong){
                        $latlong=explode(',',$bookingRequest->to_latlong);
                        if(count($latlong)==2){
                            $lat = $latlong[0];
                            $long = $latlong[1];
                        }
                     }

                     $newTripRequest[$nkey]['bidid']=$bookingRequest->id;
                     $newTripRequest[$nkey]['request_id']=$bookingRequest->request_id;
                     $newTripRequest[$nkey]['from_location']=$bookingRequest->from_location;
                     $newTripRequest[$nkey]['to_location']=$bookingRequest->to_location;
                     $newTripRequest[$nkey]['client_id'] = $bookingRequest->client->id;
                     $newTripRequest[$nkey]['client_name'] = $bookingRequest->client->name;
                     $newTripRequest[$nkey]['client_mobile'] = $bookingRequest->client->mobile;
                     $newTripRequest[$nkey]['client_rating'] = $client_rating;
                     $newTripRequest[$nkey]['lat'] = $lat;
                     $newTripRequest[$nkey]['lng'] = $long;
                     
                    if($bookingRequest->driver_id==0 && $bookingRequest->prices->count()>0){
                        $client_rating = getUserRating($bookingRequest->client_id);
                        $rkey=0;
                            foreach ($bookingRequest->prices as $keyr=>$price) {
                                        $prices=[];
                                        $prices[$rkey]['price_id'] = $price->id;
                                        $prices[$rkey]['client_name'] = $price->client->name;
                                        $prices[$rkey]['mobile'] = $price->client->mobile;
                                        $prices[$rkey]['price'] =  $price->price;
                                        $prices[$rkey]['is_accepted'] = $price->is_accepted;
                                        $newTripRequest[$nkey]['prices']= $prices;
                                        $rkey++;
                                        
                            }
                    }
                 $nkey++;
             }
             
            // for pending 
            $dtpending = BookingRequest::with(['prices' => function($query) use ($user) {
                $query->where('driver_id', $user->id)
                      ->where('price', '>', 0) // Correct use of '!='
                      ->where('is_accepted', '0')
                      ->where('skip', '0');
            }])->where('status', '0')
              ->where('is_deleted', '0')
              ->orderBy('created_at', 'desc')
              ->get();
            $pendingRequest =[];
            $prices=[];
            $pkey=0;
            foreach ($dtpending as $key=>$bookingRequest) {
                   $lat ='';
                   $long='';
                   if($bookingRequest->to_latlong){
                       $latlong=explode(',',$bookingRequest->to_latlong);
                       if(count($latlong)==2){
                           $lat = $latlong[0];
                           $long = $latlong[1];
                       }
                   }
                   if($bookingRequest->driver_id==0 && $bookingRequest->prices->count()>0){
                       $client_rating = getUserRating($bookingRequest->client_id);
                       $npkey=0;
                       $pendingRequest[$pkey]['bidid']=$bookingRequest->id;
                       $pendingRequest[$pkey]['request_id']=$bookingRequest->request_id;
                       $pendingRequest[$pkey]['from_location']=$bookingRequest->from_location;
                       $pendingRequest[$pkey]['to_location']=$bookingRequest->to_location;
                       $pendingRequest[$pkey]['client_id'] = $bookingRequest->client->id;
                       $pendingRequest[$pkey]['client_name'] = $bookingRequest->client->name;
                       $pendingRequest[$pkey]['client_mobile'] = $bookingRequest->client->mobile;
                       $pendingRequest[$pkey]['client_rating'] = $client_rating;
                       $pendingRequest[$pkey]['lat'] = $lat;
                       $pendingRequest[$pkey]['lng'] = $long;
                        foreach ($bookingRequest->prices as $keyr=>$price) {
                               $prices=[];
                               $prices[$npkey]['price_id'] = $price->id;
                               $prices[$npkey]['client_name'] = $price->client->name;
                               $prices[$npkey]['mobile'] = $price->client->mobile;
                               $prices[$npkey]['price'] =  $price->price;
                               $prices[$npkey]['is_accepted'] = $price->is_accepted;
                               $pendingRequest[$pkey]['prices']= $prices; 
                               $npkey++;
                       }
                       $pkey++;
                    
                   }
               }
             
             //$data['newTripRequest']= $newTripRequest;
              //ongoing trip
               $dt = BookingRequest::where('is_deleted', 0)
               ->whereHas('payment', function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               })
               ->with(['payment' => function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               }])->get();

               $orderRequest =[];
              
               $upcommingRequest =[];
               $ongoingRequest =[];
               $arrivedRequest =[];
               $canceledRequest =[];
               $completedRequest =[];
               $prices=[];
               $key0=0;
               $key1=0;
               $key2=0;
               $key3=0;
               $key4=0;
               $key5=0;
                foreach ($dt as $key=>$bookingRequest){
                    $lat ='';
                    $long='';
                    if($bookingRequest->to_latlong){
                        $latlong=explode(',',$bookingRequest->to_latlong);
                        if(count($latlong)==2){
                            $lat = $latlong[0];
                            $long = $latlong[1];
                        }
                    }
                    // if($bookingRequest->status==0 && $bookingRequest->driver_id==0 ){
                    //     BookingPrice::where('request_id',$bookingRequest->id)->where('skip',0)->first();
                    //     $pendingRequest[$key0]['bidid']=$bookingRequest->id;
                    //     $pendingRequest[$key0]['request_id']=$bookingRequest->request_id;
                    //     $pendingRequest[$key0]['from_location']=$bookingRequest->from_location;
                    //     $pendingRequest[$key0]['to_location']=$bookingRequest->to_location;
                    //     $pendingRequest[$key0]['client_id'] = $bookingRequest->client->id;
                    //     $pendingRequest[$key0]['client_name'] = $bookingRequest->client->name;
                    //     $pendingRequest[$key0]['client_mobile'] = $bookingRequest->client->mobile;
                    //     $pendingRequest[$key0]['status'] = $bookingRequest->status;
                    //     $pendingRequest[$key0]['lat'] = $lat;
                    //     $pendingRequest[$key0]['lng'] = $long;
                    //     $pendingRequest[$key0]['rating'] = $bookingRequest->rating;
                    //     $key1++ ;
                    //    }
                    if($bookingRequest->status==1 && $bookingRequest->driver_id==$driverId ){
                        $upcommingRequest[$key1]['bidid']=$bookingRequest->id;
                        $upcommingRequest[$key1]['request_id']=$bookingRequest->request_id;
                        $upcommingRequest[$key1]['from_location']=$bookingRequest->from_location;
                        $upcommingRequest[$key1]['to_location']=$bookingRequest->to_location;
                        $upcommingRequest[$key1]['client_id'] = $bookingRequest->client->id;
                        $upcommingRequest[$key1]['client_name'] = $bookingRequest->client->name;
                        $upcommingRequest[$key1]['client_mobile'] = $bookingRequest->client->mobile;
                        $upcommingRequest[$key1]['status'] = $bookingRequest->status;
                        $upcommingRequest[$key1]['lat'] = $lat;
                        $upcommingRequest[$key1]['lng'] = $long;
                        $upcommingRequest[$key1]['rating'] = $bookingRequest->rating;
                        $key1++ ;
                       }

                    if($bookingRequest->status==2 && $bookingRequest->driver_id==$driverId){ //arrived
                        $arrivedRequest[$key2]['bidid']=$bookingRequest->id;
                        $arrivedRequest[$key2]['request_id']=$bookingRequest->request_id;
                        $arrivedRequest[$key2]['from_location']=$bookingRequest->from_location;
                        $arrivedRequest[$key2]['to_location']=$bookingRequest->to_location;
                        $arrivedRequest[$key2]['client_id'] = $bookingRequest->client->id;
                        $arrivedRequest[$key2]['client_name'] = $bookingRequest->client->name;
                        $arrivedRequest[$key2]['client_mobile'] = $bookingRequest->client->mobile;
                        $arrivedRequest[$key2]['status'] = $bookingRequest->status;
                        $arrivedRequest[$key2]['lat'] = $lat;
                        $arrivedRequest[$key2]['lng'] = $long;
                        $arrivedRequest[$key2]['rating'] = $bookingRequest->rating;
                      $key2++;
                    }
                    if($bookingRequest->status==3 && $bookingRequest->driver_id==$driverId){ //ongoing
                        $ongoingRequest[$key3]['bidid']=$bookingRequest->id;
                        $ongoingRequest[$key3]['request_id']=$bookingRequest->request_id;
                        $ongoingRequest[$key3]['from_location']=$bookingRequest->from_location;
                        $ongoingRequest[$key3]['to_location']=$bookingRequest->to_location;
                        $ongoingRequest[$key3]['client_id'] = $bookingRequest->client->id;
                        $ongoingRequest[$key3]['client_name'] = $bookingRequest->client->name;
                        $ongoingRequest[$key3]['client_mobile'] = $bookingRequest->client->mobile;
                        $ongoingRequest[$key3]['status'] = $bookingRequest->status;
                        $ongoingRequest[$key3]['lat'] = $lat;
                        $ongoingRequest[$key3]['lng'] = $long;
                        $ongoingRequest[$key3]['rating'] = $bookingRequest->rating;
                     $key3++;
                    }
                   if($bookingRequest->status==4 && $bookingRequest->driver_id==$driverId){ // canceled
                        $canceledRequest[$key4]['bidid']=$bookingRequest->id;
                        $canceledRequest[$key4]['request_id']=$bookingRequest->request_id;
                        $canceledRequest[$key4]['from_location']=$bookingRequest->from_location;
                        $canceledRequest[$key4]['to_location']=$bookingRequest->to_location;
                        $canceledRequest[$key4]['client_id'] = $bookingRequest->client->id;
                        $canceledRequest[$key4]['client_name'] = $bookingRequest->client->name;
                        $canceledRequest[$key4]['client_mobile'] = $bookingRequest->client->mobile;
                        $canceledRequest[$key4]['status'] = $bookingRequest->status;
                        $canceledRequest[$key4]['lat'] = $lat;
                        $canceledRequest[$key4]['lng'] = $long;
                        $canceledRequest[$key4]['rating'] = $bookingRequest->rating;
                      $key4++;
                    }
                    if($bookingRequest->status==5 && $bookingRequest->driver_id==$driverId){ // completed
                        $completedRequest[$key5]['bidid']=$bookingRequest->id;
                        $completedRequest[$key5]['request_id']=$bookingRequest->request_id;
                        $completedRequest[$key5]['from_location']=$bookingRequest->from_location;
                        $completedRequest[$key5]['to_location']=$bookingRequest->to_location;
                        $completedRequest[$key5]['client_id'] = $bookingRequest->client->id;
                        $completedRequest[$key5]['client_name'] = $bookingRequest->client->name;
                        $completedRequest[$key5]['client_mobile'] = $bookingRequest->client->mobile;
                        $completedRequest[$key5]['status'] = $bookingRequest->status;
                        $completedRequest[$key5]['lat'] = $lat;
                        $completedRequest[$key5]['lng'] = $long;
                        $completedRequest[$key5]['rating'] = $bookingRequest->rating;
                       $key5++;
                    }
                       
                }
                $data['newTripRequest']= [$newTripRequest];
                $data['pendingRequest']= [$pendingRequest];
                $data['upcommingRequest']= [$upcommingRequest];
                $data['arrivedRequest']= [$arrivedRequest];
                $data['ongoingRequest']= [$ongoingRequest];
                $data['canceledRequest']= [$canceledRequest];
                $data['completedRequest']= [$completedRequest];
                return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized !!');
                return outputError($data); 
                
            }
        } catch (\Exception $e) {
            // Log or handle the exception
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

    public function GetDriverHistories(Request $request){
        $data = array();
        $today = Carbon::today();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
         $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->first();
            //var_dump($user);
            if ($user) {
                
              $driverId = $user->id;
               $data['message']=_lang('get Order request');
               $todayRequests = BookingRequest::where('is_deleted', 0)
               ->whereHas('payment', function($query) use ($today, $driverId) {
                //$query->whereDate('created_at', $today)
                $query->where('driver_id', $driverId);
            })
            ->with(['payment' => function($query) use ($today, $driverId) {
                //$query->whereDate('created_at', $today)
                $query->where('driver_id', $driverId);
            }])
            ->get();

            $totalEarnings = @$todayRequests->sum(function($bookingRequest) {
                return $bookingRequest->payment->payment_amount;
            });

            $totalDistance = $todayRequests->sum('distances'); // Assuming 'distance' is a field in BookingRequest
            $totalRequests = $todayRequests->count();
               $data['status']= [
                'total_earnings' => $totalEarnings,
                'total_distance' => $totalDistance,
                'total_orders' => $totalRequests,
               ];
                //ongoing trip
               $dt = BookingRequest::where('is_deleted', 0)
               ->whereHas('payment', function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               })
               ->with(['payment' => function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               }])->get();
               $orderRequest =[];
              
               $completedRequest =[];
               $prices=[];
               //dd($dt);
               $bkey=0;
                foreach ($dt as $key=>$bookingRequest){
                    $createdAt = $bookingRequest->created_at;
                    $hour = $createdAt->format('g');
                    $hour = $hour < 10 ? '100' : $hour;
                    // Format the rest
                    $formattedDate = $createdAt->format('dM ') . $hour . $createdAt->format(':iA');
                    $payment = BookingPayment::where('request_id', $bookingRequest->id)->first();
                    if($payment){
                        $prices=   BookingPrice::where('request_id', $bookingRequest->id)->where('driver_id', $payment->driver_id)->first();
                    }
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
                        // $rating = getUserRating($bookingRequest->client_id);
                        // $completedRequest[$bkey]['bidid']=$bookingRequest->id;
                        // $completedRequest[$bkey]['request_id']=$bookingRequest->request_id;
                        // $completedRequest[$bkey]['from_location']=$bookingRequest->from_location;
                        // $completedRequest[$bkey]['to_location']=$bookingRequest->to_location;
                        // $completedRequest[$bkey]['client_name'] = $bookingRequest->client->name;
                        // $completedRequest[$bkey]['client_mobile'] = $bookingRequest->client->mobile;
                        // $completedRequest[$bkey]['rating'] = $rating;
                        // $completedRequest[$bkey]['status'] = $bookingRequest->status;
                        // $completedRequest[$bkey]['from_lat'] = $from_lat;
                        // $completedRequest[$bkey]['from_lng'] = $from_long;
                        // $completedRequest[$bkey]['to_lat'] = $to_lat;
                        // $completedRequest[$bkey]['to_lng'] = $to_long;
                        // if($bookingRequest->payment){
                        //     $completedRequest[$bkey]['trip_cost'] = $bookingRequest->payment->payment_amount;
                        // }

                        $client_rating = getUserRating($bookingRequest->client_id);
                        $orderRequest[$bkey]['bidid']=$bookingRequest->id;
                        $orderRequest[$bkey]['request_id']=$bookingRequest->request_id;
                        $orderRequest[$bkey]['from_location']=$bookingRequest->from_location;
                        $orderRequest[$bkey]['to_location']=$bookingRequest->to_location;
                        $orderRequest[$bkey]['client_id'] = $bookingRequest->client->id;
                        $orderRequest[$bkey]['client_name'] = $bookingRequest->client->name;
                        $orderRequest[$bkey]['client_mobile'] = $bookingRequest->client->mobile;
                        $orderRequest[$bkey]['client_avator'] = $bookingRequest->client->avator;
                        $orderRequest[$bkey]['client_rating'] = $client_rating;
                        if($bookingRequest->driver_id>0){
                            $driver_rating = getUserRating($bookingRequest->driver->id);
                            $orderRequest[$bkey]['driver_id'] = $bookingRequest->driver->id;
                            $orderRequest[$bkey]['driver_name'] = $bookingRequest->driver->name;
                            $orderRequest[$bkey]['driver_mobile'] = $bookingRequest->driver->mobile;
                            $orderRequest[$bkey]['driver_avator'] = $bookingRequest->driver->avator; 
                            $orderRequest[$bkey]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                            $orderRequest[$bkey]['driver_rating'] = $driver_rating;
                        }
                        $orderRequest[$bkey]['status'] = $bookingRequest->status;
                        $orderRequest[$bkey]['from_lat'] = $from_lat;
                        $orderRequest[$bkey]['from_lng'] = $from_long;
                        $orderRequest[$bkey]['to_lat'] = $to_lat;
                        $orderRequest[$bkey]['to_lng'] = $to_long;
                        $orderRequest[$bkey]['time'] = $formattedDate;
                       if($payment){
                         $orderRequest[$bkey]['payment_status'] = $payment->payment_status; 
                         $orderRequest[$bkey]['bid_amount'] = $payment->payment_amount; 
                         $orderRequest[$bkey]['coupon_discount'] = $payment->coupon_discount; 
                         $orderRequest[$bkey]['coupon_code'] = $payment->coupon_code; 
                         $orderRequest[$bkey]['trip_cost'] = $payment->payment_amount; 
                       }
                       $orderRequest[$bkey]['trip_start'] = $bookingRequest->start_time;
                       $orderRequest[$bkey]['trip_end'] = $bookingRequest->end_time;
                     
                       $bkey++;  
                }
                $data['orderHistory']= [$orderRequest];
               return outputSuccess($data);
                // Proceed with authenticated user logic
                
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized');
                return outputError($data); 
                
            }
        } catch (\Exception $e) {
            // Log or handle the exception
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
