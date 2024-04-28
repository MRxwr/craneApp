<?php
namespace Modules\AppUser\Http\Livewire\Api;


use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function sendOTP(Request $request)
    {
        $data=array();
        $otp = mt_rand(1000, 9999); // Generate a random OTP
        $mobileNumber = $request->input('mobile_number');
        $user = OtpUser::where('mobile', $mobileNumber)->first();
        $data['otp'] = $otp;
        if ($user) {
            $user->otp = $otp;
            $user->save();
            return outputSuccess($data);
        } else {
           // Create a new OtpUser
            $otpUser = OtpUser::create([
                'otp' => $otp,
                'mobile' => $mobileNumber,
            ]);
        }
        return outputSuccess($data);
    }

    public function verifyOTP(Request $request){ 
        $data=array();
        $otp = $request->input('otp');
        $mobileNumber = $request->input('mobile_number');
        $storedOTP = OtpUser::where('mobile', $mobileNumber)->first();
        if (!$storedOTP) {
            $data['message']=_lang('OTP not found');
            return outputError($data);
        }
        if ($otp == $storedOTP->otp) {
            // OTP matched, mark it as verified in the database
            OtpUser::where('mobile', $mobileNumber)->update(['verified' => true]);
            $data['mobile']=$mobileNumber;
            $data['message']=_lang('OTP verified successfully');
            return outputSuccess($data);
        } else {
            $data['message']=_lang('Invalid OTP');
            return outputError($data);
        }
    }
}
