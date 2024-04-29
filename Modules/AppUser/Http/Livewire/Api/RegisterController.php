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

class RegisterController extends Controller
{
    
    public function registerClient(Request $request){
        $data = array();
        $rules = [
            'name' => 'required|string|max:255',
            'dob' => 'required|string|min:8',
            'password' => 'required|string|min:8',
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
        $name = $request->input('name');
        $email = $request->input('email');
        $dob = $request->input('dob');
        $password =$request->password;
        $isverified = OtpUser::where('mobile', $mobileNumber)->where('verified', 1)->first();
        if($isverified){
            $appuser = AppUser::where('mobile', $mobileNumber)->first();
            if ($appuser){
                $appuser->name = $name;
                $appuser->email = $email;
                $appuser->mobile = $mobileNumber;
                $appuser->dob = $dob;
                $appuser->password = bcrypt($request->password);
                $appuser->email_verified_at = now();
                $appuser->save();
                
                $data['message']=_lang('Successful loggedin');
                $credentials = $request->only('mobile', 'password');
                if (Auth::guard('api')->validate($credentials)) {
                    $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
                        Auth::guard('api')->setToken($token);
                        $data['user']= $user->toArray();
                        $data['token']= $token;
                        return outputSuccess($data);
                    }
                   
                } else {
                    $data['message']=_lang('login faild Regiter');
                    return outputError($data);
                }
                
            }else{
                $appuser = new AppUser;
                $appuser->name = $name;
                $appuser->email = $email;
                $appuser->mobile = $mobileNumber;
                $appuser->dob = $dob;
                $appuser->password = bcrypt($request->password);
                $appuser->email_verified_at = now();
                $appuser->save();
                
                $data['message']=_lang('Successfully Regiter'); 
                $credentials = $request->only('mobile', 'password');
                if (Auth::guard('api')->attempt($credentials)) {
                    $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
                        Auth::guard('api')->setToken($token);
                        $data['user']= $user->toArray();
                        $data['token']= $token;
                        return outputSuccess($data);
                    }
                } else {
                    $data['message']=_lang('login faild Regiter');
                    return outputError($data);
                }
            }
            
        }else{
            $data['message']=_lang('mobile not  verified');
            return outputError($data); 
        }

    }

    public function registerDriver(Request $request){
        $data = array();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:app_users|max:255',
            'mobile' => 'required|unique:app_users|max:12',
            'dob' => 'required|string|min:8',
            'password' => 'required|string|min:8',
        ];

        // Perform validation
        $validator = Validator::make($request->all(), $rules);
        // Check if validation fails
        if ($validator->fails()) {
            $data['message']=_lang('validation error');
            $data['errors'] = $validator->errors();
            return outputError($data);
        }
        $mobileNumber = $request->input('mobile');
        $mobileNumber = str_replace('+', '', $mobileNumber);
        $name = $request->input('name');
        $email = $request->input('email');
        $dob = $request->input('dob');
        $password =$request->password;
        $appuser = AppUser::where('mobile', $mobileNumber)->first();
        if ($appuser){
            $appuser->name = $name;
            $appuser->email = $email;
            $appuser->mobile = $mobileNumber;
            $appuser->dob = $dob;
            $appuser->password = bcrypt($request->password);
            $appuser->email_verified_at = now();
            $appuser->user_type = 2;
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Regiter');
            $credentials = $request->only('mobile', 'password');
            if (Auth::guard('api')->attempt($credentials)) {
                $user = Auth::guard('api')->user();
                if($token=GenerateApiToken($user)){
                        Auth::guard('api')->setToken($token);
                        $data['user']= $user->toArray();
                        $data['token']= $token;
                        return outputSuccess($data);
                    }
            } else {
                $data['message']=_lang('login faild Regiter');
                return outputError($data);
            }
        }else{
            $appuser = new AppUser;
            $appuser->name = $name;
            $appuser->email = $email;
            $appuser->mobile = $mobileNumber;
            $appuser->dob = $dob;
            $appuser->password = bcrypt($request->password);
            $appuser->email_verified_at = now();
            $appuser->user_type = 2;
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Regiter'); 
            $credentials = $request->only('mobile', 'password');
            if (Auth::guard('api')->attempt($credentials)) {
                $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
                        Auth::guard('api')->setToken($token);
                        $data['user']= $user->toArray();
                        $data['token']= $token;
                        return outputSuccess($data);
                    }
            } else {
                $data['message']=_lang('login faild Regiter');
                return outputError($data);
            }
        }
    }
}
