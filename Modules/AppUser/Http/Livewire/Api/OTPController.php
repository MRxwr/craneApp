<?php
namespace Modules\AppUser\Http\Livewire\Api;


use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
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
        if ($user) {
            $user->otp = $otp;
            $user->mobile = $mobileNumber;
            $user->verified = true;
            $user->save();
            $msg = str_replace('{{OTP}}', $otp, _lang('Use {{OTP}} as your login code for The Crane. Please do not share this code with anyone.'));
            //sendSMS($msg,$mobileNumber,$flag=0);
            return outputSuccess($data);
        } else {
           // Create a new OtpUser
            $otpUser = OtpUser::create([
                'otp' => $otp,
                'mobile' => $mobileNumber,
                'verified' => true,
            ]);
            $msg = str_replace('{{OTP}}', $otp, _lang('Use {{OTP}} as your login code for The Crane. Please do not share this code with anyone.'));
            //sendSMS($msg,$mobileNumber,$flag=0);
            return outputSuccess($data);
        }
        
    }

    public function verifyOTP(Request $request){ 
        $data=array();
        $otp = $request->input('otp');
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $storedOTP = OtpUser::where('mobile', $mobileNumber)->first();
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
    
}
