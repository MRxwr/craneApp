<?php
namespace Modules\BookingRequest\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\BookingPayment;
use Modules\BookingRequest\Entities\DriverPosition;
use Modules\AppUser\Entities\AppUser;
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                // Authentication successful
                $rebook=0;
                $driver_id=0;
                if($request->input('rebook')){
                    $rebook = $request->input('rebook'); 
                }
                if($request->input('driver_id')){
                    $driver_id = $request->input('driver_id');
                }
                $data['message']=_lang('Send Crane Request');
                $bidr= new BookingRequest();
                $bidr->request_id = time();
                $bidr->client_id = $user->id;
                $bidr->from_location = $request->input('from_location');
                $bidr->to_location = $request->input('to_location');
                $bidr->from_latlong = $request->input('from_latlong');
                $bidr->to_latlong = $request->input('to_latlong');
                $bidr->service_id = $request->input('service_id');
                $bidr->distances = $request->input('distances');
                $bidr->driver_id = $driver_id;
                $bidr->rebook = $rebook;
                if($bidr->save()){
                    $drivers = AppUser::where('user_type', 2)->where('is_active',1)->where('is_deleted',0)->get();
                     if($drivers->count()>0){
                        foreach($drivers as $driver){
                            if($driver->id>0){
                                if( $driver->id==$driver_id){

                                }else{
                                    $price = new  BookingPrice();
                                    $price->request_id =$bidr->id;
                                    $price->client_id =$user->id;
                                    $price->driver_id = $driver->id; 
                                    if($price->save()){
                                        $notify=[];
                                        $notify['client_id']=$user->id;
                                        $notify['driver_id']= $driver->id;
                                        $notify['message']=_lang('Notification to driver for new order');
                                        $notify['notifyTo']='driver';
                                        $user_id=$driver->id;
                                        $title=_lang('New Bid');
                                        $message=_lang('New request is available. You can start biding.');
                                        @firebaseNotification($user_id,$title,$message,$data=[]);
                                    }
                                }
                            }
                        }
                     }
                     $activity = _lang('Added new order by ').$user->name;
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
    //For driver :: single order for driver add price 
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
               $bidid= $request->input('request_id');
               $data['message']=_lang('get Order request');
               $dt = BookingRequest::with(['prices' => function($query) use ($user) {
                        $query->where('driver_id', $user->id)->where('is_accepted', '!=', 2)->where('skip',0);
                }])->where('status', 0)->get();
                $orderRequest =[];
                $prices=[];
                foreach ($dt as $key=>$bookingRequest) {
                        $orderRequest[$key]['bidid']=$bookingRequest->id;
                        $orderRequest[$key]['request_id']=$bookingRequest->request_id;
                        $orderRequest[$key]['from_location']=$bookingRequest->from_location;
                        $orderRequest[$key]['to_location']=$bookingRequest->to_location;
                        foreach ($bookingRequest->prices as $keyr=>$price) {
                            $prices=[];
                            $prices[$keyr]['price_id'] = $price->id;
                            $prices[$keyr]['client_name'] = $price->client->name;
                            $prices[$keyr]['mobile'] = $price->client->mobile;
                            $prices[$keyr]['price'] =  $price->price;
                            $prices[$keyr]['is_accepted'] = $price->is_accepted;
                            $orderRequest[$key]['prices']= $prices;
                        }
                    }
                
                $data['orderRequest']= $orderRequest;
               return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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
    //For driver :: single order for driver add price 
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
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
                    $prices['price_id'] = $bidprice->id;
                    $prices['client_name'] = $bidprice->client->name;
                    $prices['mobile'] = $bidprice->client->mobile;
                    $prices['price'] =  $bidprice->price;
                    $prices['is_accepted'] = $bidprice->is_accepted;
                    $user_id=$bidprice->client->id;
                    $title=_lang('Driver makes bid');
                    $message=_lang('A driver has made a bid.');
                    firebaseNotification($user_id,$title,$message,$data=[]);
                }
                $data['order_request']= [$prices];
                return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
                return outputError($data);  
            }
        } catch (\Exception $e) {
            // Log or handle the exception
            $data['message']=_lang('Authentication error');
            return outputError($data);
        }
    }
       //For driver :: single order for driver add price 
   public function DriverOrdersRequestForAccept(Request $request){ 
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
             
               $data['message']=_lang('get Order request For Accept');
               $dt = BookingRequest::with(['payment' => function($query) use ($user) {
                        $query->where('driver_id', $user->id);
                }])->where('driver_id', 0)->where('status', 0)->where('is_deleted', 0)->get();
                // $dt = BookingRequest::with('prices')->with('payment')->where('client_id', $user->id)->where('is_deleted', 0)->get();
                $orderRequest =[];
               $prices=[];
                foreach ($dt as $key=>$bookingRequest) {
                        $orderRequest[$key]['bidid']=$bookingRequest->id;
                        $orderRequest[$key]['request_id']=$bookingRequest->request_id;
                        $orderRequest[$key]['from_location']=$bookingRequest->from_location;
                        $orderRequest[$key]['to_location']=$bookingRequest->to_location;
                        foreach ($bookingRequest->prices as $keyr=>$price) {
                            $prices=[];
                            $prices[$keyr]['price_id'] = $price->id;
                            $prices[$keyr]['client_name'] = $price->client->name;
                            $prices[$keyr]['mobile'] = $price->client->mobile;
                            $prices[$keyr]['price'] =  $price->price;
                            $prices[$keyr]['is_accepted'] = $price->is_accepted;
                            $orderRequest[$key]['prices']= $prices;
                        }
                    }
                
                    $data['orderRequestForConfirm']= $orderRequest;
               return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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
    //For driver/client :: just update order status like upcoming,ongoing,completed 
    public function changeOrderStatus(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = BookingRequest::with('prices')->find($bidid);
                $dt->status = $request->input('status');
                if($dt->save()){
                    if($request->input('status')==5 && $dt->start_time ){
                        $dt->end_time = Carbon::now();
                        $dt->status = 5;
                        $activity = _lang('Driver reached to the Drop location and Trip is completed by ').$user->name;
                        AddBookingLog($dt,$activity);
                        $data['message']=_lang('Driver reached to the Drop location and Trip is completed by').$user->name;
                    }else{
                        $status =$dt->status;
                        $activity = _lang('Changed order status to '.$status.'  by  ').$user->name;
                        AddBookingLog($dt,$activity);
                        $data['message']=_lang('Successfully change Status');
                    }
                     return outputSuccess($data);
                } 
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

    public function cancelTheOrder(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $dt = BookingRequest::with('prices')->find($bidid);
                
                $dt->is_active = 4;
                $dt->status = 4;
                if($dt->save()){
                    if($payment=$dt->payment()->where('payment_status','success')->first()){
                       // dd($payment->payment_amount);
                        $wallet= floatval(getUserMeta('wallet',$user->id));
                        $price= floatval($payment->payment_amount);
                        $newwalletValue=$wallet+$price;
                        upadteUserMeta('wallet',$newwalletValue,$user->id);
                          $wdata['request_id']=$dt->id;
                          $wdata['app_user_id']=$user->id;
                          $wdata['amount']=$price;
                          $wdata['mode']='credit';
                          $wdata['remark']=_lang('Trip has been successfully canceled and refunded to wallet , canceled by ').$user->name;
                        walletTransaction($wdata);
                        $activity = _lang('Trip has been successfully canceled and refunded to wallet , canceled by ').$user->name;
                        AddBookingLog($dt,$activity);
                    }else{
                        $data['message']=_lang('Trip has been successfully canceled  by ').$user->name;
                        $activity=_lang('Trip has been successfully canceled  by ').$user->name;
                        AddBookingLog($dt,$activity);
                    }
                     $status =4;
                     //$activity = _lang('Canceled the order  by  ').$user->name;
                     if($user->user_type==1){
                        $user_id=$user->id;
                        $title=_lang('Canceled trip');
                        $message=_lang('Trip has been cancelled by client.');
                        firebaseNotification($user_id,$title,$message='',$data=[]);
                     }else{
                        $user_id=$user->id;
                        $title=_lang('Canceled trip');
                        $message=_lang('Trip has been cancelled by driver and refunded to your wallet.');
                        firebaseNotification($user_id,$title,$message='',$data=[]);
                     }
                     $data['message']=_lang('Trip has been cancelled by driver and refunded to your wallet');
                     return outputSuccess($data);
                } 
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
    // save order rating 
    public function saveOrderRating(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = BookingRequest::with('prices')->find($bidid);
                $dt->rating = $request->input('rating');
                if($dt->save()){
                     $rating =$dt->rating;
                     $activity = _lang('Gives order rating  '.$rating.'  by  ').$user->name;
                     AddBookingLog($dt,$activity);
                     $data['message']=_lang('Successfully added your rating');
                     return outputSuccess($data);
                } 
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
    // save order Start time / end tive 
    public function saveOrderStartEnd(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $activity = '';
                $bidid= $request->input('request_id');
                $data['message']=_lang('Save order start/end');
                $dt = BookingRequest::with('prices')->find($bidid);
                if($dt){
                    if($dt->status==1 && $dt->started==''){
                            //$dt->end_time = Carbon::now();
                            $dt->started = 'started';
                            $dt->status=1;
                            $activity = _lang('Trip started by  ').$user->name;
                            $data['message']=_lang('Order started by  ').$user->name;
                            if($dt->save()){
                                AddBookingLog($dt,$activity);
                                return outputSuccess($data);
                            } 
                        }else if($dt->status==1 && $dt->started=='started'){
                        $dt->start_time = Carbon::now();
                        $dt->started = 'pickup';
                        $dt->status = 3;
                        $activity = _lang('Driver reached to the pickup location and Trip on going by ').$user->name;
                        //$activity = _lang('Trip on going by  ').$user->name;
                        $data['message']=_lang('Driver reached to the pickup location and Trip on going by  ').$user->name;
                        if($dt->save()){
                            AddBookingLog($dt,$activity);
                            return outputSuccess($data);
                        }    
                   
                    }
                    
                }else{
                    $data['message']=_lang('Order not found');
                    return outputError($data);
                }
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

    //trauck driver position  driver_positions

    public function TrackDriverPosition(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = DriverPosition::where('request_id',$bidid)->first;
                if($dt){
                     $dt->time= $request->input('time');
                     $dt->distance= $request->input('distance');
                }else{
                     $dt = new DriverPosition;
                     $dt->time= $request->input('time');
                     $dt->distance= $request->input('distance');   
                }
                if($dt->save()){
                    $data['message']=_lang('Driver Location update');
                     return outputSuccess($data);
                } 
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
    //For client : return list driver of this order
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
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
               $bidid= $request->input('request_id');
               $data['message']=_lang('Send Crane Request');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bdprices = $dt->prices()->where('price','!=','')->get();
               $prices=[];
               $driverList['bidid']=$bidid;
               $driverList['request_id']=$dt->request_id;
               $driverList['from_location']=$dt->from_location;
               $driverList['to_location']=$dt->to_location;
               $driverList['distance']=$dt->distances;
               $driverList['status']=$dt->status;
               if($bdprices){
                    foreach($bdprices as $key=>$price){
                        $prices[$key]['price_id'] = $price->id;
                        $prices[$key]['driver_id'] = $price->driver_id;
                        $prices[$key]['driver_name'] = $price->driver->name;
                        $prices[$key]['mobile'] = $price->driver->mobile;
                        $prices[$key]['price'] =  $price->price;
                        $prices[$key]['is_accepted'] = $price->is_accepted;
                    }   
                }
               $driverList['prices']=$prices;
               $data['driverList']= [$driverList];
               return outputSuccess($data);
                // Proceed with authenticated user logic
            }else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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
    //For client : Place order with payment (knet/card/wallet)
    public function placeOrderRequest(Request $request){
        $data = array();
        $payment_data=array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'Header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();

            if ($user) {
               $bidid= $request->input('request_id');
               $payment_method=$request->input('payment_method');
               $is_wallet=$request->input('is_wallet');
               $driver_id=$request->input('driver_id');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bidprice = $dt->prices->where('driver_id', $driver_id)->first();
               $prices=[];
                  $data['message']=_lang('Place Order Request');
                if($bidprice){
                    if(checkSuccessPayment($bidid)){
                        $data['message']=_lang('This trip is already successfully paid');
                        return outputError($data); 
                    }else{
                        if($is_wallet==1){
                            $price = floatval($bidprice->price);
                            if(checkCoupon($price)){
                                $price = checkCoupon($price);
                            }
                            $wallet= floatval(getUserMeta('wallet',$user->id));
                            if($wallet>=$price){
                                $bsid=base64_encode($dt->id.'|'.$bidprice->id);
                                $newwalletValue=$wallet-$price;
                                upadteUserMeta('wallet',$newwalletValue,$user->id);
                                $wdata['request_id']=$dt->id;
                                $wdata['app_user_id']=$user->id;
                                $wdata['amount']=$price;
                                $wdata['mode']='debit';
                                $wdata['remark']=_lang('payment successfully done through wallet by ').$user->name;
                                walletTransaction($wdata);
                                $data['payment_data']['payment_type']='wallet';
                                $data['payment_data']['payment_status']='success';
                                $activity = _lang('payment successfully done through wallet by ').$user->name;
                                $remark =_lang('payment successfully done through wallet by ').$user->name;
                                $payment_type ='wallet';
                                $transaction_id=time();
                                $data['payment_data']['success_url'] = url('success').'/?bsid='.$bsid.'&paymentId='.$transaction_id.'&transaction_id='.$transaction_id;
                                $data['payment_data']['failed_url'] = url('failed').'/?bsid='.$bsid.'&paymentId='.$transaction_id.'&transaction_id='.$transaction_id;
                                if(DoBooking($dt,$transaction_id,$payment_type,$price,$remark,$driver_id)){
                                    $dt->status = 1;
                                    $dt->driver_id = $bidprice->driver_id?$bidprice->driver_id:0;
                                    $dt->save();
                                    $bidprice->is_accepted = 1;
                                    $bidprice->save();
                                    AddBookingLog($dt,$activity);
                                    return outputSuccess($data);
                                }
                            }else{
                                $data['message']=_lang('Insufficient funds in the wallet');
                                return outputError($data); 
                            }

                        }else{
                            $price = floatval($bidprice->price);
                            if(checkCoupon($price)){
                                $price = checkCoupon($price);
                            }
                            $payment_data['booking_id']=$dt->id;
                            $payment_data['price_id'] = $bidprice->id;
                            $payment_data['customer_name']=$user->name;
                            $payment_data['customer_mobile'] = $user->mobile;
                            $payment_data['customer_email'] = $user->email;;
                            $payment_data['paymentMethod'] = $payment_method;
                            $payment_data['pay_amount']= $price;
                            $payment_data['driver_id']= $driver_id;
                            $remark =_lang('payment ongoing through payapi by ').$user->name;
                            $payment_type ='knet/card';
                            $transaction_id='';
                            if(DoBooking($dt,$transaction_id,$payment_type,$price,$remark, $driver_id)){
                                $pdata = $this->doPayment($payment_data);
                                $activity = _lang('payment ongoing  through payapi by ').$user->name;
                                AddBookingLog($dt,$activity);  
                                $driverList[$bidid]['prices']=$prices;
                                $data['payment_data']= $pdata;
                                return outputSuccess($data);
                            } 
                        }
                       
                   }
                }
                // Proceed with authenticated user logic
            }else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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
    //payment action 
    public function doPayment($payment_data){
        $bsid=base64_encode($payment_data['booking_id'].'|'.$payment_data['price_id']);
        //$PaymentAPIKey = 'CKW-1640114323-2537';
        //$PaymentAPIKey = 'CKW-1720608905-1840';
        $PaymentAPIKey = getSetting('payapi_key');
        $paymentMethod=$payment_data['paymentMethod'];
        $name = $payment_data['customer_name'];
        $phone1 = $payment_data['customer_mobile'];
        $cutomierMobile = substr($phone1,0,11);
        $settingsEmail = $payment_data['customer_email'];
        
        $totalPrice = $payment_data['pay_amount'];
        $driver_id=$payment_data['driver_id'];
        $driver = AppUser::find($driver_id);
        $driver_iban = $driver->iban;
        $admin_amount =1;
        if(getSetting('chargeType')=='percentage'){
            $admin_amount = $totalPrice * getSetting('chargeAmount') / 100;
            $rest_of_amount = $totalPrice - $admin_amount;
        }else if(getSetting('chargeType')=='fixed'){
            $admin_amount = getSetting('chargeAmount'); 
            $rest_of_amount = $totalPrice - $admin_amount;
        }else{
            $admin_amount = 1.5;
            $rest_of_amount = $totalPrice - $admin_amount; 
        }
        $extraMerchantData= array();
        // $extraMerchantData=array(
        //     0=>array(
        //         'amount'=>(string)$admin_amount,
        //         'knetCharge'=>'0.25',
        //         'knetChargeType'=>'fixed',
        //         'ccCharge'=>'0.25',
        //         'ccChargeType'=>'fixed',
        //         'ibanNumber'=>getSetting('mainIban')
        //     ),
        //     1=>array(
        //         'amount'=>(string)$rest_of_amount,
        //         'knetCharge'=>'0.25',
        //         'knetChargeType'=>'fixed',
        //         'ccCharge'=>'0.25',
        //         'ccChargeType'=>'fixed',
        //         'ibanNumber'=>$driver_iban
        //     )
        // );  
        $titmestamp = time();
        $orderId = $titmestamp;
        $paymentGateway = 'knet';
        // $params = array(
        //     "endpoint"                  => "PaymentRequestExicuteForVendors",
        //     "apikey"                    => $PaymentAPIKey,
        //     "PaymentMethodId"           => $paymentMethod,
        //     "CustomerName"              => $name,
        //     "DisplayCurrencyIso"        => "KWD", 
        //     "MobileCountryCode"         => "+965", 
        //     "CustomerMobile"            => substr($phone1,0,11),
        //     "CustomerEmail"             => $settingsEmail,
        //     "InvoiceValue"              => $totalPrice,
        //     "SourceInfo"                => '',
        //     "CallBackUrl"               => url('success').'/?bsid='.$bsid,
        //     "ErrorUrl"                  => url('failed').'/?bsid='.$bsid,
        //     "extraMerchantData[0][amount]" => (string)$admin_amount,
        //     "extraMerchantData[0][knetCharge]" => '0.25',
        //     "extraMerchantData[0][knetChargeType]" => 'fixed',
        //     "extraMerchantData[0][ccCharge]" => '0.25',
        //     "extraMerchantData[0][ccChargeType]" => 'fixed',
        //     "extraMerchantData[0][ibanNumber]" => getSetting('mainIban'),
        //     "extraMerchantData[1][amount]" => (string)$rest_of_amount,
        //     "extraMerchantData[1][knetCharge]" => '0.25',
        //     "extraMerchantData[1][knetChargeType]" => 'fixed',
        //     "extraMerchantData[1][ccCharge]" => '0.25',
        //     "extraMerchantData[1][ccChargeType]" => 'fixed',
        //     "extraMerchantData[1][ibanNumber]" => $driver_iban,
        //     );
            $postBody = array(
                'language' => 'en',
                'paymentGateway[src]' => "{$paymentGateway}",
                'order[id]' => $orderId,
                'order[currency]' => 'KWD',
                'order[amount]' => (string)$totalPrice,
                'order[description]' => "Booking payment",
                'reference[id]' => $orderId,
                'customer[name]' => "{$name}",
                'customer[email]' => "{$settingsEmail}",
                'customer[mobile]' => "{$cutomierMobile}",
                'returnUrl' => url('success').'/?bsid='.$bsid,
                'cancelUrl' => url('failed').'/?bsid='.$bsid,
                'notificationUrl' => url('failed').'/?bsid='.$bsid,
                'extraMerchantData[0][amount]' =>  (string)$admin_amount,
                'extraMerchantData[0][knetCharge]' => '0.25',
                'extraMerchantData[0][knetChargeType]' => 'fixed',
                'extraMerchantData[0][ccCharge]' => '0.25',
                'extraMerchantData[0][ccChargeType]' => 'fixed',
                'extraMerchantData[0][ibanNumber]' => getSetting('mainIban'),
                'extraMerchantData[1][amount]' => (string)$rest_of_amount,
                'extraMerchantData[1][knetCharge]' => '0.25',
                'extraMerchantData[1][knetChargeType]' => 'fixed',
                'extraMerchantData[1][ccCharge]' => '0.25',
                'extraMerchantData[1][ccChargeType]' => 'fixed',
                'extraMerchantData[1][ibanNumber]' => "{$driver_iban}",
                );
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://uapi.upayments.com/api/v1/charge',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postBody,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer afmceR6nHQaIehhpOel036LBhC8hihuB8iNh9ACF',
                ),
            ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $response = json_decode($response,true);
 
    //saving info and redirecting to payment pages
    if ($err) {
        echo "cURL Error #:" . $err;
        $error_url = url('failed').'/?bsid='.$bsid.'&msg='. $err;
        $data['payment_status']='error';
        $data['error_url']= url('failed').'/?bsid='.$bsid.'&msg='. $err;
    } else {
        
        if( isset($response["status"]) && $response["status"] == true && isset($response["data"]["link"]) && !empty($response["data"]["link"]) ){
            $data['message']=_lang('Send Crane Request');
            $data['payment_status']='success';
            $data['payment_type']='knet/card';
            $data['payment_url'] = $response["data"]["link"];
            // $response["data"] = array(
            //     "paymentURL" => $response["data"]["link"],
            //     "InvoiceId"  => $orderId
            // );
        }else{
            $data['message']=_lang('Send Crane Request');
            $data['payment_status']='error';
            $data['error_url']= url('failed').'/?bsid='.$bsid.'&msg='. $err;
        }
    }

       return $data;
    }
    //For client : return list driver of this order
    public function getOderDetails(Request $request){
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
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            $OrderDetails=[];
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = BookingRequest::with('prices')->with('payment')->find($bidid);
                $to_lat ='';
                $to_long='';
                $from_lat ='';
                $from_long='';
                if($dt->to_latlong){
                    $Tolatlong=explode(',',$dt->to_latlong);
                    if(count($Tolatlong)==2){
                        $to_lat = $Tolatlong[0];
                        $to_long = $Tolatlong[1];
                    }
                }
                if($dt->from_latlong){
                    $Fromlatlong=explode(',',$dt->from_latlong);
                    if(count($Fromlatlong)==2){
                        $from_lat = $Fromlatlong[0];
                        $from_long = $Fromlatlong[1];
                    }
                }
                $payment = BookingPayment::where('request_id', $bidid)->first();
                $bdprices = $dt->prices()->where('is_accepted', 1)->first();
                $driver_rating = getUserRating($bdprices->driver->id);
                $prices=[];
                $client_rating = getUserRating($dt->client_id);
                $OrderDetails['bidid']=$bidid;
                $OrderDetails['request_id']=$dt->request_id;
                $OrderDetails['from_location']=$dt->from_location;
                $OrderDetails['to_location']=$dt->to_location;
                $OrderDetails['client_id'] = $dt->client->id;
                $OrderDetails['client_name'] = $dt->client->name;
                $OrderDetails['client_mobile'] = $dt->client->mobile;
                $OrderDetails['client_avator'] = $dt->client->avator;
                $OrderDetails['client_rating'] = $client_rating;
                $OrderDetails['status']=$dt->status;
                $OrderDetails['price_id'] = $bdprices->id;
                $OrderDetails['driver_name'] = $bdprices->driver->name;
                $OrderDetails['mobile'] = $bdprices->driver->mobile;
                $OrderDetails['driver_id'] = $bdprices->driver->id;
                $OrderDetails['driver_avator'] = $bookingRequest->driver->avator; 
                $OrderDetails['login_status'] = AppUserLogingStatus($bookingRequest->driver->id); 
                $OrderDetails['driver_rating'] = $driver_rating;
                $OrderDetails['price'] =  $bdprices->price;
                $OrderDetails['from_lat'] = $from_lat;
                $OrderDetails['from_lng'] = $from_long;
                $OrderDetails['to_lat'] = $to_lat;
                $OrderDetails['to_lng'] = $to_long;
                $OrderDetails['is_accepted'] = $bdprices->is_accepted;
                if($payment){
                    $OrderDetails['payment_status'] = $payment->payment_status; 
                    $OrderDetails['bid_amount'] = $payment->payment_amount; 
                    $OrderDetails['coupon_discount'] = $payment->coupon_discount; 
                    $OrderDetails['coupon_code'] = $payment->coupon_code; 
                    $OrderDetails['trip_cost'] = $payment->payment_amount; 
                  }
                $OrderDetails['trip_start'] = $dt->start_time;
                $OrderDetails['trip_end'] = $dt->end_time;
                $data['orderDetails']= [$OrderDetails];
               return outputSuccess($data);
                // Proceed with authenticated user logic
            }else {
                // Authentication failed
                $data['message']=_lang('Unauthorized due to token mismatch');
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

     //For client : return list driver of this order
     public function getClientDriverToken(Request $request){
        $data = array();
        
        try {
           
            $OrderDetails=[];
                $bidid= $request->input('request_id');
                $data['message']=_lang('Device token Request');
                $dt = BookingRequest::with('prices')->with('payment')->find($bidid);
                $OrderDetails['bidid']=$dt->id;
                if($dt->client){
                    $OrderDetails['client_token'] = $dt->client->device_token;
                }else{
                    $OrderDetails['client_token'] = '';
                }
                if($dt->driver){
                    $OrderDetails['driver_token'] = $dt->driver->device_token;
                }else{
                    $OrderDetails['driver_token'] = '';
                }
                $data['device']= [$OrderDetails];
               return outputSuccess($data);
                // Proceed with authenticated user logic
            
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
    public function doDriverOrderSkip(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = BookingPrice::where('request_id',$bidid)->where('driver_id',$user->id)->first();
              if($dt){
                $dt->skip = 1;
                if($dt->save()){
                     $activity = _lang('The order has been Skipped by ').$user->name;
                     AddBookingLog($dt,$activity);
                     $data['message']=_lang('The order has been Skipped');
                     $data['status'] = true;
                     return outputSuccess($data);
                } else{
                    $data['status'] = true;
                    $data['message']=_lang('The order has not Skipped');
                    $data['status'] = false;
                    return outputError($data);
                }
            }else{
               $data['message']=_lang('Trip not found');
               return outputError($data);   
            }
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

    public function getSuccess(Request $request)
    {
        $data=[];
        $token = $request->header('Authorization');
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                if($request->paymentId && $request->bsid){
                    $bsid = $request->bsid;
                    $paymentId = $request->paymentId;
                    if($bsid && $paymentId){
                        $decodedData = base64_decode($bsid);
                        $ids=explode('|',$decodedData);
                        
                        if(!empty($ids)){
                            $data=[];
                            $bidid = $ids[0];
                            $pid = $ids[1];
                            $price = BookingPrice::find($pid);
                            $dt = BookingRequest::with('prices')->find($bidid);
                            $dt->status = 1;
                            $dt->driver_id = $price->driver_id?$price->driver_id:0;
                            $dt->save();
                            $price->is_accepted = 1;
                            $price->save();
                            $payment=BookingPayment::where('request_id',$bidid)->first();
                            $payment->driver_id = $price->driver_id?$price->driver_id:0;
                            $payment->transaction_id=$paymentId;
                            $payment->payment_status='success';
                            $payment->save();
                            BookingPrice::where('request_id',$bidid)->where('driver_id','!=',$price->driver_id)->update(['skip'=>1]);
                            $data['message'] =' Payment Successfully Done';
                            $data['price'] = $price;
                            $data['payment'] = $payment; 
                            $user_id=$payment->driver_id;
                            $title=_lang('new trip');
                            $message=_lang('Client paid successfully. Please start your trip ASAP.');
                            
                            @firebaseNotification($user_id,$title,$message='',[]);
                            
                            return outputSuccess($data);
                        }
                    }else{
                        $data['message']=_lang('bsid & PaymentId are missing');
                        return outputError($data); 
                    }
                }else{
                    $data['message']=_lang('link is not valied');
                    return outputError($data); 
                }
            }else{
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
    public function getFailed(Request $request)
    { 
        $data=[];
        $token = $request->header('Authorization');
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        try {
            $token = str_replace('Bearer ', '', $token);
            $user = AppUser::where('token',$token)->where('is_deleted',0)->first();
            if ($user) {
                if($request->paymentId && $request->bsid){
                    $bsid = $request->bsid;
                    $paymentId = $request->paymentId;
                
                if($bsid && $paymentId){
                    $decodedData = base64_decode($bsid);
                    $ids=explode('|',$decodedData);
                    
                    if(!empty($ids)){
                        $remark =_lang('Payment Declined');
                        $data=[];
                        $bidid = $ids[0];
                        $pid = $ids[1];
                        $dt = BookingRequest::with('prices')->find($bidid);
                        $price = BookingPrice::find($pid);
                        $payment=BookingPayment::where('request_id',$bidid)->first();
                        $payment->driver_id = $price->driver_id?$price->driver_id:0;
                        $payment->transaction_id='';
                        //$payment->payment_amount='';
                        $payment->remark=$remark;
                        $payment->payment_status='failed';
                        $payment->save();
                        //$data['dt'] = $dt;
                        $data['price'] = $price;
                        $data['payment'] = $payment; 
                        $data['message'] =' Payment failed';
                        return outputSuccess($data);
                    }
                    }else{
                        $data['message']=_lang('bsid & PaymentId are missing');
                        return outputError($data); 
                    }
    
                }else{
                    $data['message']=_lang('link is not valied');
                    return outputError($data); 
                }
            }else{
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
