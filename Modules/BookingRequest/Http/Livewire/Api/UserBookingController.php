<?php
namespace Modules\BookingRequest\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\LoginAttempt;
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
            $user = AppUser::where('token',$token)->first();
            if ($user) {
               $data['message']=_lang('get Order request');
               $dt = BookingRequest::where('client_id', $user->id)->where('is_deleted', 0)->get();
               $orderRequest =[];
               $prices=[];
                foreach ($dt as $bookingRequest){
                        $orderRequest[$bookingRequest->id]['bidid']=$bookingRequest->id;
                        $orderRequest[$bookingRequest->id]['request_id']=$bookingRequest->request_id;
                        $orderRequest[$bookingRequest->id]['from_location']=$bookingRequest->from_location;
                        $orderRequest[$bookingRequest->id]['to_location']=$bookingRequest->to_location;
                        foreach ($bookingRequest->prices as $price) {
                            $prices=[];
                            $prices[$price->id]['price_id'] = $price->id;
                            $prices[$price->id]['client_name'] = $price->client->name;
                            $prices[$price->id]['mobile'] = $price->client->mobile;
                            $prices[$price->id]['price'] =  $price->price;
                            $prices[$price->id]['is_accepted'] = $price->is_accepted;
                            $orderRequest[$bookingRequest->id]['prices']= $prices;
                        }
                }
                
                $data['orderRequest']= $orderRequest;
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
            $user = AppUser::where('token',$token)->first();
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
                return $bookingRequest->payment->payment_amount;
            });

            $totalDistance = $todayRequests->sum('distances'); // Assuming 'distance' is a field in BookingRequest
            $totalRequests = $todayRequests->count();
               $data['todayEarnings']= [
                'total_earnings' => $totalEarnings,
                'total_distance' => $totalDistance,
                'total_trips' => $totalRequests,
                'time_online'=>$totalLoginTimeFormatted
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
               $ongoingRequest =[];
               $upcommingRequest =[];
               $arrivedRequest =[];
               $canceledRequest =[];
               $completedRequest =[];
               $prices=[];
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

                    if($bookingRequest->status==1){
                         $upcommingRequest[$key]['bidid']=$bookingRequest->id;
                         $upcommingRequest[$key]['request_id']=$bookingRequest->request_id;
                         $upcommingRequest[$key]['from_location']=$bookingRequest->from_location;
                         $upcommingRequest[$key]['to_location']=$bookingRequest->to_location;
                         $upcommingRequest[$key]['client_name'] = $bookingRequest->client->name;
                         $upcommingRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                         $upcommingRequest[$key]['status'] = $bookingRequest->status;
                         $upcommingRequest[$key]['lat'] = $lat;
                         $upcommingRequest[$key]['lng'] = $long;
                         $upcommingRequest[$key]['rating'] = $bookingRequest->rating;
                    }
                    if($bookingRequest->status==2){
                        $arrivedRequest[$key]['bidid']=$bookingRequest->id;
                        $arrivedRequest[$key]['request_id']=$bookingRequest->request_id;
                        $arrivedRequest[$key]['from_location']=$bookingRequest->from_location;
                        $arrivedRequest[$key]['to_location']=$bookingRequest->to_location;
                        $arrivedRequest[$key]['client_name'] = $bookingRequest->client->name;
                        $arrivedRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                        $arrivedRequest[$key]['status'] = $bookingRequest->status;
                        $arrivedRequest[$key]['lat'] = $lat;
                        $arrivedRequest[$key]['lng'] = $long;
                        $arrivedRequest[$key]['rating'] = $bookingRequest->rating;
                   }
                    if($bookingRequest->status==3){
                        $ongoingRequest[$key]['bidid']=$bookingRequest->id;
                        $ongoingRequest[$key]['request_id']=$bookingRequest->request_id;
                        $ongoingRequest[$key]['from_location']=$bookingRequest->from_location;
                        $ongoingRequest[$key]['to_location']=$bookingRequest->to_location;
                        $ongoingRequest[$key]['client_name'] = $bookingRequest->client->name;
                        $ongoingRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                        $ongoingRequest[$key]['status'] = $bookingRequest->status;
                        $ongoingRequest[$key]['lat'] = $lat;
                        $ongoingRequest[$key]['lng'] = $long;
                        $ongoingRequest[$key]['rating'] = $bookingRequest->rating;
                   }
                   if($bookingRequest->status==4){
                        $canceledRequest[$key]['bidid']=$bookingRequest->id;
                        $canceledRequest[$key]['request_id']=$bookingRequest->request_id;
                        $canceledRequest[$key]['from_location']=$bookingRequest->from_location;
                        $canceledRequest[$key]['to_location']=$bookingRequest->to_location;
                        $canceledRequest[$key]['client_name'] = $bookingRequest->client->name;
                        $canceledRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                        $canceledRequest[$key]['status'] = $bookingRequest->status;
                        $canceledRequest[$key]['lat'] = $lat;
                        $canceledRequest[$key]['lng'] = $long;
                        $canceledRequest[$key]['rating'] = $bookingRequest->rating;
                    }
                    if($bookingRequest->status==5){
                        $completedRequest[$key]['bidid']=$bookingRequest->id;
                        $completedRequest[$key]['request_id']=$bookingRequest->request_id;
                        $completedRequest[$key]['from_location']=$bookingRequest->from_location;
                        $completedRequest[$key]['to_location']=$bookingRequest->to_location;
                        $completedRequest[$key]['client_name'] = $bookingRequest->client->name;
                        $completedRequest[$key]['client_mobile'] = $bookingRequest->client->mobile;
                        $completedRequest[$key]['status'] = $bookingRequest->status;
                        $canceledRequest[$key]['lat'] = $lat;
                        $canceledRequest[$key]['lng'] = $long;
                        $completedRequest[$key]['rating'] = $bookingRequest->rating;
                   }
                       
                }
                
                $data['upcommingRequest']= [$ongoingRequest];
                $data['arrivedRequest']= [$ongoingRequest];
                $data['ongoingRequest']= [$ongoingRequest];
                $data['canceledRequest']= [$ongoingRequest];
                $data['completedRequest']= [$ongoingRequest];
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
