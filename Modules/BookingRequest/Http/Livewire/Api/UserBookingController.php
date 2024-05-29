<?php
namespace Modules\BookingRequest\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
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
            var_dump($user);
            if ($user) {
                $driverId = $user->id;
               $data['message']=_lang('get Order request');
               $todayEarnings = BookingRequest::where('is_deleted', 0)
                    ->whereHas('payment', function($query) use ($today, $driverId) {
                        $query->whereDate('created_at', $today)
                            ->where('driver_id', $driverId);
                    })->with(['payment' => function($query) use ($today, $driverId) {
                        $query->whereDate('created_at', $today)
                            ->where('driver_id', $driverId);
                    }])->get()->sum(function($bookingRequest) {
                        return $bookingRequest->payment->amount;
                    });
               $data['todayEarnings']= $orderRequest;
               $dt = BookingRequest::where('is_deleted', 0)
               ->whereHas('payment', function($query) use ($today, $driverId) {
                $query->where('driver_id', $driverId);
                })->get();
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
