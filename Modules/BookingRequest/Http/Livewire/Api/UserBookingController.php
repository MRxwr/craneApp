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
               //$dt = BookingRequest::with('prices')->with('payment')->where('client_id', $user->id)->where('is_deleted', 0)->whereIn('status',['',0,1,2,3,4,5])->get();
               $dt = BookingRequest::with('prices')
               ->with('payment')
               ->where('client_id', $user->id)
               ->where('is_deleted', 0)
               ->whereIn('status', ['', 0, 1, 2, 3, 4, 5])
               ->orderBy('created_at', 'desc')
               ->get();

               $orderRequest =[];
               $prices=[];
                foreach ($dt as $key=>$bookingRequest){
                    $start_time = $bookingRequest->start_time ? Carbon::parse($bookingRequest->start_time) : null;
                    $end_time = $bookingRequest->end_time ? Carbon::parse($bookingRequest->end_time) : null;
                    if ($start_time!='' && $end_time!='') {
                        $total_seconds = $end_time->diffInSeconds($start_time);
                    } else {
                        $total_seconds = 0; // Or handle when one of the times is null
                    }
                    
                    
                        $createdAt = $bookingRequest->created_at;
                    
                        // Format the rest
                        $formattedDate = formatDateWithOrdinal($createdAt);
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
                        $orderRequest[$key]['distance']=$bookingRequest->distances;
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
                        }else{
                            //$driver_rating = getUserRating($bookingRequest->driver->id);
                            $orderRequest[$key]['driver_id'] = 0;
                            $orderRequest[$key]['driver_name'] = '';
                            $orderRequest[$key]['driver_mobile'] = '';
                            $orderRequest[$key]['driver_avator'] = 'assets/img/default-user.jpg';; 
                            $orderRequest[$key]['login_status'] = 0; 
                            $orderRequest[$key]['driver_rating'] ='';
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
                       $orderRequest[$key]['time_interval'] = $total_seconds; 
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
            ->orderBy('created_at', 'desc')
            ->get();
            $totalEarnings = $todayRequests->sum(function($bookingRequest) {
                return @$bookingRequest->payment->payment_amount;
            });
            $totalDistance = $todayRequests->sum('distances'); // Assuming 'distance' is a field in BookingRequest
            $totalRequests = $todayRequests->count();
               $data['todayEarnings']= [
                'total_earnings' => number_format((float)$totalEarnings,  3, '.', ''),
                'total_distance' => $totalDistance,
                'total_trips' => $totalRequests,
                'time_online'=>number_format((float)$totalLoginTimeFormatted, 2, '.', ''),
                'login_status' => AppUserLogingStatus($driverId)
            ];

            
            // $dtnew = BookingRequest::with(['prices' => function($query) use ($user) {
            //          $query->where('driver_id', $user->id)->where('is_accepted', '0')->where('skip',0);
            //  }])->where('status', '0')->where('is_deleted',0)->orderBy('created_at', 'desc')->get();
            //  $newTripRequest =[];
            //  $pendingRequest =[];
            //  $prices=[];
            //  $nkey=0;
            //  $pkey=0;
            //  foreach ($dtnew as $key=>$bookingRequest) {
            //         $lat ='';
            //         $long='';
            //         $from_lat='';
            //         $from_long='';
            //         $to_lat='';
            //         $to_long='';
            //         if($bookingRequest->to_latlong){

            //             $latlong=explode(',',$bookingRequest->to_latlong);
            //             if(count($latlong)==2){
            //                 $lat = $latlong[0];
            //                 $long = $latlong[1];
            //                 $to_lat=$latlong[0];;
            //                 $to_long=$latlong[1];
            //             }
            //         }
            //         if($bookingRequest->from_latlong){

            //             $latlong=explode(',',$bookingRequest->from_latlong);
            //             if(count($latlong)==2){
            //                 $lat = $latlong[0];
            //                 $long = $latlong[1];
            //                 $from_lat=$latlong[0];;
            //                 $from_long=$latlong[1];
            //             }
            //         }
            //         if($bookingRequest->driver_id==0 && $bookingRequest->prices->count()>0){
            //             $client_rating = getUserRating($bookingRequest->client_id);
            //             $rkey=0;
            //             $npkey=0;
            //             $numpen=0;
            //             $numnew=0;
            //          foreach ($bookingRequest->prices as $keyr=>$price) {
            //                 if(!$price->price && $numnew==0){
            //                     $newTripRequest[$nkey]['bidid']=$bookingRequest->id;
            //                     $newTripRequest[$nkey]['request_id']=$bookingRequest->request_id;
            //                     $newTripRequest[$nkey]['from_location']=$bookingRequest->from_location;
            //                     $newTripRequest[$nkey]['to_location']=$bookingRequest->to_location;
            //                     $newTripRequest[$nkey]['client_id'] = $bookingRequest->client->id;
            //                     $newTripRequest[$nkey]['client_name'] = $bookingRequest->client->name;
            //                     $newTripRequest[$nkey]['client_mobile'] = $bookingRequest->client->mobile;
            //                     $newTripRequest[$nkey]['client_rating'] = $client_rating;
            //                     $newTripRequest[$nkey]['lat'] = $lat;
            //                     $newTripRequest[$nkey]['lng'] = $long;
            //                     $newTripRequest[$nkey]['to_lat'] = $to_lat;
            //                     $newTripRequest[$nkey]['to_lng'] = $to_long;
            //                     $newTripRequest[$nkey]['from_lat'] = $from_lat;
            //                     $newTripRequest[$nkey]['from_lng'] = $from_long;
            //                     $prices=[];
            //                     $prices[$rkey]['price_id'] = $price->id;
            //                     $prices[$rkey]['client_name'] = $price->client->name;
            //                     $prices[$rkey]['mobile'] = $price->client->mobile;
            //                     $prices[$rkey]['price'] =  $price->price;
            //                     $prices[$rkey]['is_accepted'] = $price->is_accepted;
            //                     $newTripRequest[$nkey]['prices']= $prices;
            //                     $nkey++;
            //                     $numnew++;
            //                 }else if($price->price && $numpen==0){
            //                     $pendingRequest[$pkey]['bidid']=$bookingRequest->id;
            //                     $pendingRequest[$pkey]['request_id']=$bookingRequest->request_id;
            //                     $pendingRequest[$pkey]['from_location']=$bookingRequest->from_location;
            //                     $pendingRequest[$pkey]['to_location']=$bookingRequest->to_location;
            //                     $pendingRequest[$pkey]['client_id'] = $bookingRequest->client->id;
            //                     $pendingRequest[$pkey]['client_name'] = $bookingRequest->client->name;
            //                     $pendingRequest[$pkey]['client_mobile'] = $bookingRequest->client->mobile;
            //                     $pendingRequest[$pkey]['client_rating'] = $client_rating;
            //                     $pendingRequest[$pkey]['lat'] = $lat;
            //                     $pendingRequest[$pkey]['lng'] = $long;
            //                     $pendingRequest[$pkey]['to_lat'] = $to_lat;
            //                     $pendingRequest[$pkey]['to_lng'] = $to_long;
            //                     $pendingRequest[$pkey]['from_lat'] = $from_lat;
            //                     $pendingRequest[$pkey]['from_lng'] = $from_long;
            //                     
           // $prices=[];
            //                     $prices[$npkey]['price_id'] = $price->id;
            //                     $prices[$npkey]['client_name'] = $price->client->name;
            //                     $prices[$npkey]['mobile'] = $price->client->mobile;
            //                     $prices[$npkey]['price'] =  $price->price;
            //                     $prices[$npkey]['is_accepted'] = $price->is_accepted;
                                
            //                     $pendingRequest[$pkey]['prices']= $prices; 
            //                     $rkey++;
            //                     $numpen++;
            //                 }
            //             }
                     
            //         }
            //     }



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
                        $from_lat='';
                        $from_long='';
                        $to_lat='';
                        $to_long='';
                        if($bookingRequest->to_latlong){
    
                            $latlong=explode(',',$bookingRequest->to_latlong);
                            if(count($latlong)==2){
                                $lat = $latlong[0];
                                $long = $latlong[1];
                                $to_lat=$latlong[0];;
                                $to_long=$latlong[1];
                            }
                        }
                        if($bookingRequest->from_latlong){
    
                            $latlong=explode(',',$bookingRequest->from_latlong);
                            if(count($latlong)==2){
                                $lat = $latlong[0];
                                $long = $latlong[1];
                                $from_lat=$latlong[0];;
                                $from_long=$latlong[1];
                            }
                        }
                        $client_rating = getUserRating($bookingRequest->client_id);
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
                        $newTripRequest[$nkey]['to_lat'] = $to_lat;
                        $newTripRequest[$nkey]['to_lng'] = $to_long;
                        $newTripRequest[$nkey]['from_lat'] = $from_lat;
                        $newTripRequest[$nkey]['from_lng'] = $from_long;
                         
                        if($bookingRequest->driver_id==0 && $bookingRequest->prices->count()>0){
                            
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
                          ->where('price', '!=', '') // Correct use of '!='
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
                       $from_lat='';
                       $from_long='';
                       $to_lat='';
                       $to_long='';
                       if($bookingRequest->to_latlong){
   
                           $latlong=explode(',',$bookingRequest->to_latlong);
                           if(count($latlong)==2){
                               $lat = $latlong[0];
                               $long = $latlong[1];
                               $to_lat=$latlong[0];;
                               $to_long=$latlong[1];
                           }
                       }
                       if($bookingRequest->from_latlong){
   
                           $latlong=explode(',',$bookingRequest->from_latlong);
                           if(count($latlong)==2){
                               $lat = $latlong[0];
                               $long = $latlong[1];
                               $from_lat=$latlong[0];;
                               $from_long=$latlong[1];
                           }
                       }
                        $client_rating = getUserRating($bookingRequest->client_id);
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
                            $pendingRequest[$pkey]['to_lat'] = $to_lat;
                            $pendingRequest[$pkey]['to_lng'] = $to_long;
                            $pendingRequest[$pkey]['from_lat'] = $from_lat;
                            $pendingRequest[$pkey]['from_lng'] = $from_long;
                            
                       if($bookingRequest->driver_id==0 && $bookingRequest->prices->count()>0){
                           //$client_rating = getUserRating($bookingRequest->client_id);
                           $npkey=0;
                          
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
                           
                        
                       }
                       $pkey++;
                   }
             
             //$data['newTripRequest']= $newTripRequest;
              //ongoing trip
               $dt = BookingRequest::where('is_deleted', 0)
               ->whereHas('payment', function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               })
               ->with(['payment' => function($query) use ($driverId) {
                   $query->where('driver_id', $driverId);
               }])->orderBy('created_at', 'desc')->get();

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
                            $to_lat=$latlong[0];;
                            $to_long=$latlong[1];
                        }
                    }
                    if($bookingRequest->from_latlong){

                        $latlong=explode(',',$bookingRequest->from_latlong);
                        if(count($latlong)==2){
                            $lat = $latlong[0];
                            $long = $latlong[1];
                            $from_lat=$latlong[0];;
                            $from_long=$latlong[1];
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
                        $upcommingRequest[$key1]['to_lat'] = $to_lat;
                        $upcommingRequest[$key1]['to_lng'] = $to_long;
                        $upcommingRequest[$key1]['from_lat'] = $from_lat;
                        $upcommingRequest[$key1]['from_lng'] = $from_long;
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
                        $arrivedRequest[$key2]['to_lat'] = $to_lat;
                        $arrivedRequest[$key2]['to_lng'] = $to_long;
                        $arrivedRequest[$key2]['from_lat'] = $from_lat;
                        $arrivedRequest[$key2]['from_lng'] = $from_long;
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
                        $ongoingRequest[$key3]['to_lat'] = $to_lat;
                        $ongoingRequest[$key3]['to_lng'] = $to_long;
                        $ongoingRequest[$key3]['from_lat'] = $from_lat;
                        $ongoingRequest[$key3]['from_lng'] = $from_long;
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
                        $canceledRequest[$key4]['to_lat'] = $to_lat;
                        $canceledRequest[$key4]['to_lng'] = $to_long;
                        $canceledRequest[$key4]['from_lat'] = $from_lat;
                        $canceledRequest[$key4]['from_lng'] = $from_long;
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
                        $completedRequest[$key5]['to_lat'] = $to_lat;
                        $completedRequest[$key5]['to_lng'] = $to_long;
                        $completedRequest[$key5]['from_lat'] = $from_lat;
                        $completedRequest[$key5]['from_lng'] = $from_long;
                        $completedRequest[$key5]['rating'] = $bookingRequest->rating;
                       $key5++;
                    }
                       
                }
                $data['newTripRequest']= [$newTripRequest];
                $data['pendingRequest']= [$pendingRequest];
                $data['upcommingRequest']= [$upcommingRequest];
                //$data['arrivedRequest']= [$arrivedRequest];
                $data['ongoingRequest']= [$ongoingRequest];
                //$data['canceledRequest']= [$canceledRequest];
                //$data['completedRequest']= [$completedRequest];
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
            ->where('status', 5)
            ->get();

            $totalEarnings = @$todayRequests->sum(function($bookingRequest) {
                return $bookingRequest->payment->payment_amount;
            });

            $totalDistance = $todayRequests->sum('distances'); // Assuming 'distance' is a field in BookingRequest
            $totalRequests = $todayRequests->count();
               $data['status']= [
                'total_earnings' => number_format((float)$totalEarnings, 3, '.', ''),
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
                $pendingRequest =[];
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
                    $createdAt = $bookingRequest->created_at;
                    $hour = $createdAt->format('g');
                    $hour = $hour < 10 ? '100' : $hour;
                    // Format the rest
                    $formattedDate = $createdAt->format('dM ') . $hour . $createdAt->format(':iA');
                    $payment = BookingPayment::where('request_id', $bookingRequest->id)->first();
                    $prices=   BookingPrice::where('request_id', $bookingRequest->id)->where('driver_id', $driverId)->first();
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

                        if($bookingRequest->status==0 && $bookingRequest->driver_id==$driverId ){
                            $client_rating = getUserRating($bookingRequest->client_id);
                            $pendingRequest[$key0]['bidid']=$bookingRequest->id;
                            $pendingRequest[$key0]['request_id']=$bookingRequest->request_id;
                            $pendingRequest[$key0]['from_location']=$bookingRequest->from_location;
                            $pendingRequest[$key0]['to_location']=$bookingRequest->to_location;
                            $pendingRequest[$key0]['client_id'] = $bookingRequest->client->id;
                            $pendingRequest[$key0]['client_name'] = $bookingRequest->client->name;
                            $pendingRequest[$key0]['client_mobile'] = $bookingRequest->client->mobile;
                            $pendingRequest[$key0]['client_avator'] = $bookingRequest->client->avator;
                            $pendingRequest[$key0]['client_rating'] = $client_rating;
                            if($prices->driver_id>0){
                                $driver_rating = getUserRating($prices->driver_id);
                                $pendingRequest[$key0]['driver_id'] = $prices->driver->id;
                                $pendingRequest[$key0]['driver_name'] = $prices->driver->name;
                                $pendingRequest[$key0]['driver_mobile'] = $prices->driver->mobile;
                                $pendingRequest[$key0]['driver_avator'] = $prices->driver->avator; 
                                $pendingRequest[$key0]['login_status'] = AppUserLogingStatus($prices->driver->id); 
                                $pendingRequest[$key0]['driver_rating'] = $driver_rating;
                            }else{
                                //$driver_rating = getUserRating($bookingRequest->driver->id);
                                $pendingRequest[$key0]['driver_id'] = 0;
                                $pendingRequest[$key0]['driver_name'] = '';
                                $pendingRequest[$key0]['driver_mobile'] = '';
                                $pendingRequest[$key0]['driver_avator'] = 'assets/img/default-user.jpg';; 
                                $pendingRequest[$key0]['login_status'] = 0; 
                                $pendingRequest[$key0]['driver_rating'] ='';
                            }
                            $pendingRequest[$key0]['status'] = $bookingRequest->status;
                            $pendingRequest[$key0]['from_lat'] = $from_lat;
                            $pendingRequest[$key0]['from_lng'] = $from_long;
                            $pendingRequest[$key0]['to_lat'] = $to_lat;
                            $pendingRequest[$key0]['to_lng'] = $to_long;
                            $pendingRequest[$key0]['time'] = $formattedDate;
                            if($payment){
                                $pendingRequest[$key0]['payment_status'] = $payment->payment_status; 
                                $pendingRequest[$key0]['bid_amount'] = $payment->payment_amount; 
                                $pendingRequest[$key0]['coupon_discount'] = $payment->coupon_discount; 
                                $pendingRequest[$key0]['coupon_code'] = $payment->coupon_code; 
                                $pendingRequest[$key0]['trip_cost'] = $payment->payment_amount; 
                            }
                            $upcommingRequest[$key0]['trip_start'] = $bookingRequest->start_time;
                            $upcommingRequest[$key0]['trip_end'] = $bookingRequest->end_time; 
                            $key0++ ;
                    }
                     if($bookingRequest->status==1 && $bookingRequest->driver_id==$driverId ){
                                $client_rating = getUserRating($bookingRequest->client_id);
                                $upcommingRequest[$key1]['bidid']=$bookingRequest->id;
                                $upcommingRequest[$key1]['request_id']=$bookingRequest->request_id;
                                $upcommingRequest[$key1]['from_location']=$bookingRequest->from_location;
                                $upcommingRequest[$key1]['to_location']=$bookingRequest->to_location;
                                $upcommingRequest[$key1]['client_id'] = $bookingRequest->client->id;
                                $upcommingRequest[$key1]['client_name'] = $bookingRequest->client->name;
                                $upcommingRequest[$key1]['client_mobile'] = $bookingRequest->client->mobile;
                                $upcommingRequest[$key1]['client_avator'] = $bookingRequest->client->avator;
                                $upcommingRequest[$key1]['client_rating'] = $client_rating;
                                if($bookingRequest->driver_id>0){
                                    $driver_rating = getUserRating($bookingRequest->driver->id);
                                    $upcommingRequest[$key1]['driver_id'] = $bookingRequest->driver->id;
                                    $upcommingRequest[$key1]['driver_name'] = $bookingRequest->driver->name;
                                    $upcommingRequest[$key1]['driver_mobile'] = $bookingRequest->driver->mobile;
                                    $upcommingRequest[$key1]['driver_avator'] = $bookingRequest->driver->avator; 
                                    $upcommingRequest[$key1]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                                    $upcommingRequest[$key1]['driver_rating'] = $driver_rating;
                                }else{
                                    //$driver_rating = getUserRating($bookingRequest->driver->id);
                                    $upcommingRequest[$key1]['driver_id'] = 0;
                                    $upcommingRequest[$key1]['driver_name'] = '';
                                    $upcommingRequest[$key1]['driver_mobile'] = '';
                                    $upcommingRequest[$key1]['driver_avator'] = 'assets/img/default-user.jpg';; 
                                    $upcommingRequest[$key1]['login_status'] = 0; 
                                    $upcommingRequest[$key1]['driver_rating'] ='';
                                }
                                $upcommingRequest[$key1]['status'] = $bookingRequest->status;
                                $upcommingRequest[$key1]['from_lat'] = $from_lat;
                                $upcommingRequest[$key1]['from_lng'] = $from_long;
                                $upcommingRequest[$key1]['to_lat'] = $to_lat;
                                $upcommingRequest[$key1]['to_lng'] = $to_long;
                                $upcommingRequest[$key1]['time'] = $formattedDate;
                                if($payment){
                                    $upcommingRequest[$key1]['payment_status'] = $payment->payment_status; 
                                    $upcommingRequest[$key1]['bid_amount'] = $payment->payment_amount; 
                                    $upcommingRequest[$key1]['coupon_discount'] = $payment->coupon_discount; 
                                    $upcommingRequest[$key1]['coupon_code'] = $payment->coupon_code; 
                                    $upcommingRequest[$key1]['trip_cost'] = $payment->payment_amount; 
                                }
                                $upcommingRequest[$key1]['trip_start'] = $bookingRequest->start_time;
                                $upcommingRequest[$key1]['trip_end'] = $bookingRequest->end_time; 
                                
                            $key1++ ;
                        }
                    //arrived trips
                     if($bookingRequest->status==2 && $bookingRequest->driver_id==$driverId){ //arrived
                         
                         $client_rating = getUserRating($bookingRequest->client_id);
                                $arrivedRequest[$key2]['bidid']=$bookingRequest->id;
                                $arrivedRequest[$key2]['request_id']=$bookingRequest->request_id;
                                $arrivedRequest[$key2]['from_location']=$bookingRequest->from_location;
                                $arrivedRequest[$key2]['to_location']=$bookingRequest->to_location;
                                $arrivedRequest[$key2]['client_id'] = $bookingRequest->client->id;
                                $arrivedRequest[$key2]['client_name'] = $bookingRequest->client->name;
                                $arrivedRequest[$key2]['client_mobile'] = $bookingRequest->client->mobile;
                                $arrivedRequest[$key2]['client_avator'] = $bookingRequest->client->avator;
                                $arrivedRequest[$key2]['client_rating'] = $client_rating;
                                if($bookingRequest->driver_id>0){
                                    $driver_rating = getUserRating($bookingRequest->driver->id);
                                    $arrivedRequest[$key2]['driver_id'] = $bookingRequest->driver->id;
                                    $arrivedRequest[$key2]['driver_name'] = $bookingRequest->driver->name;
                                    $arrivedRequest[$key2]['driver_mobile'] = $bookingRequest->driver->mobile;
                                    $arrivedRequest[$key2]['driver_avator'] = $bookingRequest->driver->avator; 
                                    $arrivedRequest[$key2]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                                    $arrivedRequest[$key2]['driver_rating'] = $driver_rating;
                                }else{
                                    //$driver_rating = getUserRating($bookingRequest->driver->id);
                                    $arrivedRequest[$key2]['driver_id'] = 0;
                                    $arrivedRequest[$key2]['driver_name'] = '';
                                    $arrivedRequest[$key2]['driver_mobile'] = '';
                                    $arrivedRequest[$key2]['driver_avator'] = 'assets/img/default-user.jpg';; 
                                    $arrivedRequest[$key2]['login_status'] = 0; 
                                    $arrivedRequest[$key2]['driver_rating'] ='';
                                }
                                $arrivedRequest[$key2]['status'] = $bookingRequest->status;
                                $arrivedRequest[$key2]['from_lat'] = $from_lat;
                                $arrivedRequest[$key2]['from_lng'] = $from_long;
                                $arrivedRequest[$key2]['to_lat'] = $to_lat;
                                $arrivedRequest[$key2]['to_lng'] = $to_long;
                                $arrivedRequest[$key2]['time'] = $formattedDate;
                                if($payment){
                                    $arrivedRequest[$key2]['payment_status'] = $payment->payment_status; 
                                    $arrivedRequest[$key2]['bid_amount'] = $payment->payment_amount; 
                                    $arrivedRequest[$key2]['coupon_discount'] = $payment->coupon_discount; 
                                    $arrivedRequest[$key2]['coupon_code'] = $payment->coupon_code; 
                                    $arrivedRequest[$key2]['trip_cost'] = $payment->payment_amount; 
                                }
                                $arrivedRequest[$key2]['trip_start'] = $bookingRequest->start_time;
                                $arrivedRequest[$key2]['trip_end'] = $bookingRequest->end_time; 
                            
                            $key2++;
                     }
                     //ongoing trips   
                     if($bookingRequest->status==3 && $bookingRequest->driver_id==$driverId){ //ongoing
                            $client_rating = getUserRating($bookingRequest->client_id);
                            $ongoingRequest[$key3]['bidid']=$bookingRequest->id;
                            $ongoingRequest[$key3]['request_id']=$bookingRequest->request_id;
                            $ongoingRequest[$key3]['from_location']=$bookingRequest->from_location;
                            $ongoingRequest[$key3]['to_location']=$bookingRequest->to_location;
                            $ongoingRequest[$key3]['client_id'] = $bookingRequest->client->id;
                            $ongoingRequest[$key3]['client_name'] = $bookingRequest->client->name;
                            $ongoingRequest[$key3]['client_mobile'] = $bookingRequest->client->mobile;
                            $ongoingRequest[$key3]['client_avator'] = $bookingRequest->client->avator;
                            $ongoingRequest[$key3]['client_rating'] = $client_rating;
                            if($bookingRequest->driver_id>0){
                                $driver_rating = getUserRating($bookingRequest->driver->id);
                                $ongoingRequest[$key3]['driver_id'] = $bookingRequest->driver->id;
                                $ongoingRequest[$key3]['driver_name'] = $bookingRequest->driver->name;
                                $ongoingRequest[$key3]['driver_mobile'] = $bookingRequest->driver->mobile;
                                $ongoingRequest[$key3]['driver_avator'] = $bookingRequest->driver->avator; 
                                $ongoingRequest[$key3]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                                $ongoingRequest[$key3]['driver_rating'] = $driver_rating;
                            }else{
                                //$driver_rating = getUserRating($bookingRequest->driver->id);
                                $ongoingRequest[$key3]['driver_id'] = 0;
                                $ongoingRequest[$key3]['driver_name'] = '';
                                $ongoingRequest[$key3]['driver_mobile'] = '';
                                $ongoingRequest[$key3]['driver_avator'] = 'assets/img/default-user.jpg';; 
                                $ongoingRequest[$key3]['login_status'] = 0; 
                                $ongoingRequest[$key3]['driver_rating'] ='';
                            }
                            $ongoingRequest[$key3]['status'] = $bookingRequest->status;
                            $ongoingRequest[$key3]['from_lat'] = $from_lat;
                            $ongoingRequest[$key3]['from_lng'] = $from_long;
                            $ongoingRequest[$key3]['to_lat'] = $to_lat;
                            $ongoingRequest[$key3]['to_lng'] = $to_long;
                            $ongoingRequest[$key3]['time'] = $formattedDate;
                            if($payment){
                                $ongoingRequest[$key3]['payment_status'] = $payment->payment_status; 
                                $ongoingRequest[$key3]['bid_amount'] = $payment->payment_amount; 
                                $ongoingRequest[$key3]['coupon_discount'] = $payment->coupon_discount; 
                                $ongoingRequest[$key3]['coupon_code'] = $payment->coupon_code; 
                                $ongoingRequest[$key3]['trip_cost'] = $payment->payment_amount; 
                            }
                            $ongoingRequest[$key3]['trip_start'] = $bookingRequest->start_time;
                            $ongoingRequest[$key3]['trip_end'] = $bookingRequest->end_time; 
                        
                            $key3++;
                     }
                       //cancelled trips
                    if($bookingRequest->status==4 && $bookingRequest->driver_id==$driverId){ // canceled
                            $client_rating = getUserRating($bookingRequest->client_id);
                            $canceledRequest[$key4]['bidid']=$bookingRequest->id;
                            $canceledRequest[$key4]['request_id']=$bookingRequest->request_id;
                            $canceledRequest[$key4]['from_location']=$bookingRequest->from_location;
                            $canceledRequest[$key4]['to_location']=$bookingRequest->to_location;
                            $canceledRequest[$key4]['client_id'] = $bookingRequest->client->id;
                            $canceledRequest[$key4]['client_name'] = $bookingRequest->client->name;
                            $canceledRequest[$key4]['client_mobile'] = $bookingRequest->client->mobile;
                            $canceledRequest[$key4]['client_avator'] = $bookingRequest->client->avator;
                            $canceledRequest[$key4]['client_rating'] = $client_rating;
                            if($bookingRequest->driver_id>0){
                                $driver_rating = getUserRating($bookingRequest->driver->id);
                                $canceledRequest[$key4]['driver_id'] = $bookingRequest->driver->id;
                                $canceledRequest[$key4]['driver_name'] = $bookingRequest->driver->name;
                                $canceledRequest[$key4]['driver_mobile'] = $bookingRequest->driver->mobile;
                                $canceledRequest[$key4]['driver_avator'] = $bookingRequest->driver->avator; 
                                $canceledRequest[$key4]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                                $canceledRequest[$key4]['driver_rating'] = $driver_rating;
                            }else{
                                //$driver_rating = getUserRating($bookingRequest->driver->id);
                                $canceledRequest[$key4]['driver_id'] = 0;
                                $canceledRequest[$key4]['driver_name'] = '';
                                $canceledRequest[$key4]['driver_mobile'] = '';
                                $canceledRequest[$key4]['driver_avator'] = 'assets/img/default-user.jpg';; 
                                $canceledRequest[$key4]['login_status'] = 0; 
                                $canceledRequest[$key4]['driver_rating'] ='';
                            }
                            $canceledRequest[$key4]['status'] = $bookingRequest->status;
                            $canceledRequest[$key4]['from_lat'] = $from_lat;
                            $canceledRequest[$key4]['from_lng'] = $from_long;
                            $canceledRequest[$key4]['to_lat'] = $to_lat;
                            $canceledRequest[$key4]['to_lng'] = $to_long;
                            $canceledRequest[$key4]['time'] = $formattedDate;
                        if($payment){
                            $canceledRequest[$key4]['payment_status'] = $payment->payment_status; 
                            $canceledRequest[$key4]['bid_amount'] = $payment->payment_amount; 
                            $canceledRequest[$key4]['coupon_discount'] = $payment->coupon_discount; 
                            $canceledRequest[$key4]['coupon_code'] = $payment->coupon_code; 
                            $canceledRequest[$key4]['trip_cost'] = $payment->payment_amount; 
                        }
                        $canceledRequest[$key4]['trip_start'] = $bookingRequest->start_time;
                        $canceledRequest[$key4]['trip_end'] = $bookingRequest->end_time; 
                    
                        
                         $key4++;
                     }
                     //completed trips
                     if($bookingRequest->status==5 && $bookingRequest->driver_id==$driverId){ // completed
                        $client_rating = getUserRating($bookingRequest->client_id);
                        $completedRequest[$key5]['bidid']=$bookingRequest->id;
                         $completedRequest[$key5]['request_id']=$bookingRequest->request_id;
                         $completedRequest[$key5]['from_location']=$bookingRequest->from_location;
                         $completedRequest[$key5]['to_location']=$bookingRequest->to_location;
                         $completedRequest[$key5]['client_id'] = $bookingRequest->client->id;
                         $completedRequest[$key5]['client_name'] = $bookingRequest->client->name;
                         $completedRequest[$key5]['client_mobile'] = $bookingRequest->client->mobile;
                         $completedRequest[$key5]['client_avator'] = $bookingRequest->client->avator;
                         $completedRequest[$key5]['client_rating'] = $client_rating;
                         if($bookingRequest->driver_id>0){
                             $driver_rating = getUserRating($bookingRequest->driver->id);
                             $completedRequest[$key5]['driver_id'] = $bookingRequest->driver->id;
                             $completedRequest[$key5]['driver_name'] = $bookingRequest->driver->name;
                             $completedRequest[$key5]['driver_mobile'] = $bookingRequest->driver->mobile;
                             $completedRequest[$key5]['driver_avator'] = $bookingRequest->driver->avator; 
                             $completedRequest[$key5]['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                             $completedRequest[$key5]['driver_rating'] = $driver_rating;
                         }else{
                            //$driver_rating = getUserRating($bookingRequest->driver->id);
                            $completedRequest[$key5]['driver_id'] = 0;
                            $completedRequest[$key5]['driver_name'] = '';
                            $completedRequest[$key5]['driver_mobile'] = '';
                            $completedRequest[$key5]['driver_avator'] = 'assets/img/default-user.jpg';; 
                            $completedRequest[$key5]['login_status'] = 0; 
                            $completedRequest[$key5]['driver_rating'] ='';
                        }
                         $completedRequest[$key5]['status'] = $bookingRequest->status;
                         $completedRequest[$key5]['from_lat'] = $from_lat;
                         $completedRequest[$key5]['from_lng'] = $from_long;
                         $completedRequest[$key5]['to_lat'] = $to_lat;
                         $completedRequest[$key5]['to_lng'] = $to_long;
                         $completedRequest[$key5]['time'] = $formattedDate;
                     if($payment){
                         $completedRequest[$key5]['payment_status'] = $payment->payment_status; 
                         $completedRequest[$key5]['bid_amount'] = $payment->payment_amount; 
                         $completedRequest[$key5]['coupon_discount'] = $payment->coupon_discount; 
                         $completedRequest[$key5]['coupon_code'] = $payment->coupon_code; 
                         $completedRequest[$key5]['trip_cost'] = $payment->payment_amount; 
                     }
                     $completedRequest[$key5]['trip_start'] = $bookingRequest->start_time;
                     $completedRequest[$key5]['trip_end'] = $bookingRequest->end_time; 
                         $key5++;
                     }
                        
                 }
                 $data['list'][0]['title']=_lang('Pending trips request');
                 $data['list'][0]['data']= [$pendingRequest];
                 
                 $data['list'][1]['title']=_lang('Upcomming trips request');
                 $data['list'][1]['data']= [$upcommingRequest];
                 
                 $data['list'][2]['title']=_lang('Ongoing trips request');
                 $data['list'][2]['data']= [$ongoingRequest];

                 $data['list'][3]['title']=_lang('Canceled trips request');
                 $data['list'][3]['data']= [$canceledRequest];

                 $data['list'][4]['title']=_lang('Completed trips request');
                 $data['list'][4]['data']= [$completedRequest];

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
