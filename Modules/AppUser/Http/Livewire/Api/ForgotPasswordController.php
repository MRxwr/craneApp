<?php
namespace Modules\AppUser\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Modules\AppUser\Entities\AppUserActivity;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function sendOTP(Request $request)
    {
        $data=array();
        $rules = [
            'mobile' => 'required|unique:app_users|max:12',
        ];

        // Perform validation
        $validator = Validator::make($request->all(), $rules);
        // Check if validation fails
        if ($validator->fails()) {
            $data['message']=_lang('validation error');
            $data['errors'] = $validator->errors();
            return outputError($data);
        }
        $otp = mt_rand(1000, 9999); // Generate a random OTP
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $user = OtpUser::where('mobile', $mobileNumber)->first();
        $data['otp'] = $otp;
        $isverified = OtpUser::where('mobile', $mobileNumber)->where('verified', 1)->where('type', 'register')->first();
        if($isverified){
            $appuser = AppUser::where('mobile', $mobileNumber)->where('is_deleted',0)->first();
           // Create a new OtpUser
           if( $appuser){
             $otpUser = OtpUser::create([
                'otp' => $otp,
                'mobile' => $mobileNumber,
                'type' => 'reset',
             ]);
           }
           
        return outputSuccess($data);
        } 
        
    }

    public function verifyOTP(Request $request){ 
        $data=array();
        $otp = $request->input('otp');
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $storedOTP = OtpUser::where('mobile', $mobileNumber)->where('type', 'reset')->first();
        if (!$storedOTP) {
            $data['message']=_lang('Mobile not found');
            return outputError($data);
        }
        if ($otp == $storedOTP->otp) {
            // OTP matched, mark it as verified in the database
            OtpUser::where('mobile', $mobileNumber)->update(['otp' => '','verified' => true]);
            $data['mobile']=$mobileNumber;
            $data['message']=_lang('OTP verified successfully');
            return outputSuccess($data);
        } else {
            $data['message']=_lang('Invalid OTP');
            return outputError($data);
        }
    }
    public function SetNewPassword(Request $request){
        $data = array();
        $rules = [
            'mobile' => 'required|string|max:12',
            'new_password' => 'required|string|max:12',
            'confirm_password' => 'required|string|max:12',
        ];
        // Perform validation
        $validator = Validator::make($request->all(), $rules);
        // Check if validation fails
        if ($validator->fails()) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('validation error');
            $data['errors'] = $validator->errors();
            return outputError($data);
        }
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $isverified = OtpUser::where('mobile', $mobileNumber)->where('verified', 1)->first();
        if($isverified){
            $appuser = AppUser::where('mobile', $mobileNumber)->where('is_deleted',0)->first();
            if ($appuser){
                $data['message']=_lang('Successful loggedin');
                if ($request->has('password')) {
                        // Password is provided, attempt to authenticate with password
                        $credentials = $request->only('mobile', 'password');
                        if (Auth::guard('api')->attempt($credentials)) {
                            $user = Auth::guard('api')->user();
                            if($request->input('device_token')){
                                $user->device_token = $request->input('device_token');
                                $user->save();
                            }
                            if($token=GenerateApiToken($user)){
                                $data['user']= $user->toArray();
                                $data['token']= $user->token;
                                return outputSuccess($data);
                            }
                        }else{
                            $data['message']=_lang('Authentication failed: Invalid password');
                            return outputError($data);
                        }
                    } else {
                        $mobile = $request->only('mobile');
                        $user = AppUser::where('mobile', $mobile)->first();
                        if ($user) {
                            Auth::guard('api')->user();
                            if($token=GenerateApiToken($user)){
                                if($request->input('device_token')){
                                    $user->device_token = $request->input('device_token');
                                    $user->save();
                                }
                                $data['user']= $user->toArray();
                                $data['token']= $user->token;
                                return outputSuccess($data);
                            }
                        }else {
                            $data['message']=_lang('Authentication failed: mobile number not found');
                            return outputError($data);
                        }
                     } 
                } else {
                    $data['message']=_lang('login faild Regiter');
                    return outputError($data);
                }   
        }else{
            $data['message']=_lang('mobile not  verified');
            return outputError($data); 
        }

    }

    
}
