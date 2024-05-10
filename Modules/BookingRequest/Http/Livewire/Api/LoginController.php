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
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
    public function AppUserLogin(Request $request){
        $data = array();
        $rules = [
            'mobile' => 'required|string|max:12',
            // Add more rules as needed
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
            $appuser = AppUser::where('mobile', $mobileNumber)->first();
            if ($appuser){
                $data['message']=_lang('Successful loggedin');
                if ($request->has('password')) {
                        // Password is provided, attempt to authenticate with password
                        $credentials = $request->only('mobile', 'password');
                        if (Auth::guard('api')->attempt($credentials)) {
                            $user = Auth::guard('api')->user();
                            if($token=GenerateApiToken($user)){
                                $data['user']= $user->toArray();
                                $data['token']= $token;
                                return outputSuccess($data);
                            }
                        }
                    } else {
                        $credentials = $request->only('mobile');
                        if (Auth::guard('api')->attempt($credentials)) {
                            $user = Auth::guard('api')->user();
                            if($token=GenerateApiToken($user)){
                                $data['user']= $user->toArray();
                                $data['token']= $token;
                                return outputSuccess($data);
                            }
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
