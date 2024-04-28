<?php
namespace Modules\AppUser\Http\Livewire\Api;


use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function sendOTP(Request $request)
    {
        $otp = mt_rand(1000, 9999); // Generate a random OTP
        $mobileNumber = $request->input('mobile_number');

        return response()->json(['otp' => $otp], 200);
    }

    public function verifyOTP(Request $request)
    {
        $otp = $request->input('otp');
        $mobileNumber = $request->input('mobile_number');

        // Retrieve the OTP from the database based on the mobile number
        $storedOTP = DB::table('otp_codes')->where('mobile_number', $mobileNumber)->first();

        if (!$storedOTP) {
            return response()->json(['error' => 'OTP not found'], 404);
        }

        if ($otp == $storedOTP->otp) {
            // OTP matched, mark it as verified in the database
            DB::table('otp_codes')->where('mobile_number', $mobileNumber)->update(['verified' => true]);
            return response()->json(['message' => 'OTP verified successfully'], 200);
        } else {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
    }
}
