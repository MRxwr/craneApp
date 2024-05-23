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

class UserBookingController extends Controller
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
                $bidr->service_id = $request->input('service_id');
                $bidr->distances = $request->input('distances');
                if($bidr->save()){
                    $drivers = AppUser::where('user_type', 2)->where('is_active',1)->where('is_deleted',0)->get();
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
               $data['message']=_lang('get Order request');
               $dt = BookingRequest::with(['prices' => function($query) use ($user) {
                        $query->where('driver_id', $user->id)->where('is_accepted', '!=', 2);
                }])->where('status', 0)->get();
                $orderRequest =[];
               $prices=[];
                foreach ($dt as $bookingRequest) {
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
                    $prices[$bidprice->id]['price_id'] = $bidprice->id;
                    $prices[$bidprice->id]['client_name'] = $bidprice->client->name;
                    $prices[$bidprice->id]['mobile'] = $bidprice->client->mobile;
                    $prices[$bidprice->id]['price'] =  $bidprice->price;
                    $prices[$bidprice->id]['is_accepted'] = $bidprice->is_accepted;
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
            $user = AppUser::where('token',$token)->first();
            if ($user) {
                $bidid= $request->input('request_id');
                $data['message']=_lang('Send Crane Request');
                $dt = BookingRequest::with('prices')->find($bidid);
                $dt->status = $request->input('status');
                $dt->save();
                $data['message']=_lang('Successfuly change Status');
                return outputSuccess($data);
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
            $user = AppUser::where('token',$token)->first();
            if ($user) {
               $bidid= $request->input('request_id');
               $data['message']=_lang('Send Crane Request');
               $dt = BookingRequest::with('prices')->find($bidid);
               $bdprices = $dt->prices()->where('is_accepted','!=', 2)->get();
               $prices=[];
               $driverList[$bidid]['bidid']=$bidid;
               $driverList[$bidid]['request_id']=$dt->request_id;
               $driverList[$bidid]['from_location']=$dt->from_location;
               $driverList[$bidid]['to_location']=$dt->to_location;
               $driverList[$bidid]['status']=$dt->status;
                if($bdprices){
                    foreach($bdprices as $price){
                        $prices[$price->id]['price_id'] = $price->id;
                        $prices[$price->id]['driver_name'] = $price->driver->name;
                        $prices[$price->id]['mobile'] = $price->driver->mobile;
                        $prices[$price->id]['price'] =  $price->price;
                        $prices[$price->id]['is_accepted'] = $price->is_accepted;
                    }   
                }
               $driverList[$bidid]['prices']=$prices;
               $data['driverList']= $driverList;
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
            $data['errors'] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            return outputError($data);
           
        }
    }

    public function placeOrderRequest(Request $request){
        $data = array();
        $payment_data=array();
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
               $is_wallet=$request->input('is_wallet');
               $data['message']=_lang('Place Order Request');
               $dt = BookingRequest::with('prices')->find($bidid);
                $bidprice = $dt->prices()->where('driver_id', $user->id)->first();
                $prices=[];
                if($bidprice){
                    if($is_wallet==1){
                        $price = floatval($bidprice->price);
                        $wallet= floatval(getUserMeta('wallet',$user->id));
                        if($wallet>=$price){
                            $newwalletValue=$wallet-$price;
                            upadteUserMeta('wallet',$newwalletValue,$user->id);
                            $data['payment_data']['payment_type']='wallet';
                            $data['payment_data']['payment_status']='success';
                            $activity = _lang('payment successfully done through wallet by ').$user->name;
                            AddBookingLog($dt,$activity);  
                            $driverList[$bidid]['prices']=$prices;
                            $data['payment_data']= $pdata;

                        }else{
                            $data['message']=_lang('Insufficient funds in the wallet');
                            return outputError($data); 
                        }

                    }else{
                        $price = $bidprice->price;
                        $payment_data['booking_id']=$dt->id;
                        $payment_data['price_id'] = $bidprice->id;
                        $payment_data['customer_name']=$user->name;
                        $payment_data['customer_mobile'] = $user->mobile;
                        $payment_data['customer_email'] = $user->email;;
                        $payment_data['paymentMethod'] = $payment_method;
                        $payment_data['pay_amount']= $price;
                        $pdata = $this->doPayment($payment_data);
                        //$bidprice->is_accepted = 1;
                        //$bidprice->save();
                         $activity = _lang('payment successfully done through payapi by ').$user->name;
                         AddBookingLog($dt,$activity);  
                         $driverList[$bidid]['prices']=$prices;
                         $data['payment_data']= $pdata;
                        return outputSuccess($data);
                    }
                    
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

    public function doPayment($payment_data){
        $bsid=base64_encode($payment_data['booking_id'].'|'.$payment_data['price_id']);
        $PaymentAPIKey = '';
        $paymentMethod=$payment_data['paymentMethod'];
        $name = $payment_data['customer_name'];
        $phone1 = $payment_data['customer_mobile'];
        $settingsEmail = $payment_data['customer_email'];
        $totalPrice = $payment_data['pay_amount'];

        $params = array(
            "endpoint"                  => "PaymentRequestExicuteForStore",
            "apikey"                    => "$PaymentAPIKey",
            "PaymentMethodId"           => "$paymentMethod",
            "CustomerName"              => "$name",
            "DisplayCurrencyIso"        => "KWD", 
            "MobileCountryCode"         => "+965", 
            "CustomerMobile"            => substr($phone1,0,11),
            "CustomerEmail"             => $settingsEmail,
            "invoiceValue"              => $totalPrice,
            "SourceInfo"                => '',
            "CallBackUrl"               => url('success').'/?bsid='.$bsid,
            "ErrorUrl"                  => url('failed').'/?bsid='.$bsid
            );
        $curl = curl_init();
        // $certificate_location = 'C:\wamp64\bin\php\php7.2.33\extras\ssl\cacert.pem';
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $certificate_location);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $certificate_location);
        //dd($params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://createapi.link/api/v2/index.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $data=array();
        $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                //echo "cURL Error #:" . $err;
                $error_url = url('failed').'/?bsid='.$bsid.'&msg='. $err;
                $data['payment_status']='error';
                $data['error_url']= url('failed').'/?bsid='.$bsid.'&msg='. $err;
            } else {
                $res = json_decode($response);
                if($res->type == 'success' && isset($res->data->InvoiceId)){
                    $PaymentURL = $res->data->PaymentURL;
                    $InvoiceId = $res->data->InvoiceId;
                    $data['payment_status']='success';
                    $data['payment_type']='knet/card';
                    $data['payment_url'] = $PaymentURL;  
                }else{
                    $error_url = url('payment/failed').'/?bsid='.$bsid.'&msg='.$res->msg;
                    $data['payment_status']='error';
                    $data['error_url']= $error_url;
                }
            }

       return $data;
    }
    
}
