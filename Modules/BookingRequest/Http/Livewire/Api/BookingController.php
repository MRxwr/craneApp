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

class BookingController extends Controller
{
    
    public function sendRequest(Request $request){
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
                // Authentication successful
                $data['message']=_lang('Send Crane Request');
                $bidr= new BookingRequest();
                $bidr->request_id = time();
                $bidr->client_id = $user->id;
                $bidr->from_location = $request->input('from_location');
                $bidr->to_location = $request->input('to_location');
                $bidr->from_latlong = $request->input('from_latlong');
                $bidr->to_latlong = $request->input('to_latlong');
                $bidr->distances = $request->input('distances');
                if($bidr->save()){
                    $drivers = AppUser::where('user_type', 2)->where('is_active',0)->where('is_deleted',0)->get();
                     if($drivers->count()>0){
                        foreach($drivers as $driver){
                          $price = new  BookingPrice();
                          $price->request_id =$bidr->id;
                          $price->client_id =$user->id;
                          $price->driver_id =$driver->id; 
                          if($price->save()){
                            firebaseNotification($user);
                          }
                        }
                     }
                     $activity = _lang('The Crane requested by ').$user->name;
                     AddBookingLog($bidr,$activity);
                     $data['request']= $bidr->toArray();
                    return outputSuccess($data);
                }
                
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

    public function getOrdersRequest(Request $request){
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
               $bidid= $request->input('request_id');
               $data['message']=_lang('Send Crane Request');
               $dt = BookingRequest::with('prices')->where('status',0);
               $bdprices = $dt->prices()->where('driver_id', $user->id)->where('is_accepted','!=', 2)->get();
               $prices=[];
                if($bdprices){
                    foreach($bdprices as $price){
                        $prices[$price->id]['client_name'] = $price->client->name;
                        $prices[$price->id]['mobile'] = $price->client->mobile;
                        $prices[$price->id]['price'] =  $price->price;
                        $prices[$price->id]['is_accepted'] = $price->is_accepted;
                    }   
                }
                
                $data['order_request']= $prices;
               return outputSuccess($data);
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

    public function saveOrderRequest(Request $request){
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
               $bidid= $request->input('request_id');
               $price= $request->input('price');
               $data['message']=_lang('get Driver Request');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bidprice = $dt->prices()->where('driver_id', $user->id)->first();
               $prices=[];
                if($bidprice){
                    $bidprice->price = $price;
                    $bidprice->save();
                    $activity = _lang('Added crane service price by Driver ').$user->name;
                    AddBookingLog($dt,$activity);
                    $prices[$bidprice->id]['client_name'] = $bidprice->client->name;
                    $prices[$bidprice->id]['mobile'] = $bidprice->client->mobile;
                    $prices[$bidprice->id]['price'] =  $bidprice->price;
                    $prices[$bidprice->id]['is_accepted'] = $bidprice->is_accepted;
                }
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

    public function getDriverListRequest(Request $request){
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
               $bidid= $request->input('request_id');
               $data['message']=_lang('Send Crane Request');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bdprices = $dt->prices()->where('is_accepted','!=', 2)->get();
               $prices=[];
                if($bdprices){
                    foreach($bdprices as $price){
                        $prices[$price->id]['driver_name'] = $price->driver->name;
                        $prices[$price->id]['mobile'] = $price->driver->mobile;
                        $prices[$price->id]['price'] =  $price->price;
                        $prices[$price->id]['is_accepted'] = $price->is_accepted;
                    }   
                }
               $data['driver_list']= $prices;
               return outputSuccess($data);
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

    public function placeOrderRequest(Request $request){
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
               $bidid= $request->input('request_id');
               $payment_method=$request->input('payment_method');
               $data['message']=_lang('Place Order Request');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bidprice = $dt->prices()->where('driver_id', $user->id)->first();
               $prices=[];
                if($bidprice){
                    if($payment_method=0){
                        $price = $bidprice->price;
                    }else {
                        $price = $bidprice->price;
                    }
                    

                    $bidprice->is_accepted = 1;
                    $bidprice->save();
                    $activity = _lang('Added crane service price by Driver ').$user->name;
                    AddBookingLog($dt,$activity);  
                }
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
    
}
