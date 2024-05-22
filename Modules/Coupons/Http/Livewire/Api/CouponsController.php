<?php

namespace Modules\Coupons\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Coupons\Entities\Coupon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\AppUser\Entities\AppUser;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;

class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function checkCouponCode(Request $request){
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
                $coupon_code= $request->input('coupon_code');
                $booking_amount= $request->input('amount');
                $data['message']=_lang('Send Crane Request');
                $coupon= Coupon::where('coupon_code',$coupon_code)->where('is_deleted',0)->first();
                if($coupon){
                    if($coupon->is_active==1){
                        if ($coupon->isExpired()) {
                            $data['message']=_lang('Coupon has expired');
                            return outputError($data);
                        }else{
                            if($coupon->coupon_type==1){
                              $discount = $coupon->coupon_value;
                              $new_price =  $booking_amount - $discount; 
                            }else{
                                $discount = ($booking_amount*$coupon->coupon_value/100);
                                $new_price =  $booking_amount - $discount;  
                            }
                            $data['message']=_lang('Coupon successfully applied');
                            $data['price_now'] =$new_price;
                            $data['discount'] =$discount;
                            $data['coupon_code'] =$coupon_code;
                            $data['coupon_id'] =$coupon->id;
                            return outputSuccess($data);
                        }

                    }else{
                        $data['message']=_lang('Coupon Not Active');
                        return outputError($data);
                    }

                }else{
                    $data['message']=_lang('Coupon Not exist');
                    return outputError($data);
                }

               
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
