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
            'mobile' => 'required|max:12',
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
                $otpr=OtpUser::where('mobile', $mobileNumber)->where('type', 'reset')->first();
                 if($otpr){
                    $otpr->otp = $otp;
                    $otpr->save();
                    $msg = str_replace('{{OTP}}', $otp, _lang('To reset your password for Crane, use the OTP {{OTP}}.Please do not share this code with anyone.'));
                    sendSMS($msg,$mobileNumber,$flag=0);
                    return outputSuccess($data);
                 }else{
                    $otpUser = new OtpUser();
                    $otpUser->otp =$otp;
                    $otpUser->mobile = $mobileNumber;
                    $otpUser->type = 'reset';
                    $otpUser->save();
                    $msg = str_replace('{{OTP}}', $otp, _lang('To reset your password for Crane, use the OTP {{OTP}}.Please do not share this code with anyone.'));
                    sendSMS($msg,$mobileNumber,$flag=0);
                    return outputSuccess($data);
                 } 
                  
            }else {
                $data['message']=_lang('This mobile number not exists');
                return outputError($data);
            }   
           
       
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
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
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
        $password =$request->input('password');
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $isverified = OtpUser::where('mobile', $mobileNumber)->where('verified', 1)->first();
        if($isverified){
            $appuser = AppUser::where('mobile', $mobileNumber)->where('is_deleted',0)->first();
            if ($appuser){
                $data['message']=_lang('Successful Set New Password ');
                $appuser->password = Hash::make($password);
                $appuser->save();
                return outputSuccess($data);
            } else {
                $data['message']=_lang('reset password faild ');
                return outputError($data);
            }   
        }else{
            $data['message']=_lang('mobile not  verified');
            return outputError($data); 
        }

    }

    
}
