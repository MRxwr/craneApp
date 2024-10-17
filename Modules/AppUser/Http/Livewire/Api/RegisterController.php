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
        $device_type = $request->input('device_type');
        $device_token = $request->input('device_token');
        $password =$request->password;
        $isverified = OtpUser::where('mobile', $mobileNumber)->where('verified', 1)->first();
        if($isverified){
            $appuser = AppUser::where('mobile', $mobileNumber)->first();
            if ($appuser){
                $appuser->name = $name;
                $appuser->email = $email;
                $appuser->mobile = $mobileNumber;
                $appuser->dob = $dob;
                $appuser->device_type = $device_type;
                $appuser->device_token = $device_token;
                $appuser->password = Hash::make($password);
                $appuser->email_verified_at = now();
                $appuser->is_deleted = 0;
                if ($request->hasFile('avator')) {
                    $imageName = time().'.'.$request->avator->extension();  
                    $request->avator->move(public_path('avators'), $imageName);
                    $appuser->avator = $imageName;
                }
                $appuser->save();
                
                $data['message']=_lang('Successful loggedin');
                $credentials = $request->only('mobile', 'password');
                if (Auth::guard('api')->attempt($credentials)) {
                    $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
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
                $appuser->device_type = $device_type;
                $appuser->device_token = $device_token;
                $appuser->password = Hash::make($password);
                $appuser->email_verified_at = now();
                if ($request->hasFile('avator')) {
                    $imageName = time().'.'.$request->avator->extension();  
                    $request->avator->move(public_path('avators'), $imageName);
                    $appuser->avator = $imageName;
                }
                $appuser->save();
                
                $data['message']=_lang('Successfully Regiter'); 
                $credentials = $request->only('mobile', 'password');
                if (Auth::guard('api')->attempt($credentials)) {
                    $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
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

        $otp = mt_rand(1000, 9999); // Generate a random OTP
        $user = OtpUser::where('mobile', $mobileNumber)->first();
        $data['otp'] = $otp;
        if ($user) {
            $user->otp = $otp;
            $user->mobile = $mobileNumber;
            $user->verified = true;
            $user->save(); 
        } else {
           // Create a new OtpUser
            $otpUser = OtpUser::create([
                'otp' => $otp,
                'mobile' => $mobileNumber,
                'verified' => true,
            ]);
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $dob = $request->input('dob');
        $device_type = $request->input('device_type');
        $device_token = $request->input('device_token');
        $password =$request->password;
        $appuser = AppUser::where('mobile', $mobileNumber)->first();
        if ($appuser){
            $appuser->name = $name;
            $appuser->email = $email;
            $appuser->mobile = $mobileNumber;
            $appuser->dob = $dob;
            $appuser->device_type = $device_type;
            $appuser->device_token = $device_token;
            $appuser->password = Hash::make($password);
            $appuser->email_verified_at = now();
            $appuser->user_type = 2;
            
            if ($request->hasFile('avator')) {
                $imageName = time().'.'.$request->avator->extension();  
                $request->avator->move(public_path('avators'), $imageName);
                $appuser->avator = 'avators/'.$imageName;
            }
            if ($request->hasFile('licence')) {
                $imageName = 'LNC'.time().'.'.$request->licence->extension();  
                $request->licence->move(public_path('drivers'), $imageName);
                $appuser->licence = 'drivers/'.$imageName;
            }
            if ($request->hasFile('idfront')) {
                $imageName = 'IDF'.time().'.'.$request->idfront->extension();  
                $request->idfront->move(public_path('drivers'), $imageName);
                $appuser->idfront = 'drivers/'.$imageName;
            }
            if ($request->hasFile('idback')) {
                $imageName = 'IDB'.time().'.'.$request->idback->extension();  
                $request->idback->move(public_path('drivers'), $imageName);
                $appuser->idback = 'drivers/'.$imageName;
            }
           
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Regiter');
            $credentials = $request->only('mobile', 'password');
            if (Auth::guard('api')->attempt($credentials)) {
                $user = Auth::guard('api')->user();
                if($token=GenerateApiToken($user)){
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
            $appuser->device_type = $device_type;
            $appuser->device_token = $device_token;
            $appuser->password = Hash::make($password);
            $appuser->email_verified_at = now();
            $appuser->user_type = 2;
            if ($request->hasFile('avator')) {
                $imageName = time().'.'.$request->avator->extension();  
                $request->avator->move(public_path('avators'), $imageName);
                $appuser->avator = 'avators/'.$imageName;
            }
            
            if ($request->hasFile('licence')) {
                $imageName = 'LNC'.time().'.'.$request->licence->extension();  
                $request->licence->move(public_path('drivers'), $imageName);
                $appuser->licence = 'drivers/'.$imageName;
            }
            if ($request->hasFile('idfront')) {
                $imageName = 'IDF'.time().'.'.$request->idfront->extension();  
                $request->idfront->move(public_path('drivers'), $imageName);
                $appuser->idfront = 'drivers/'.$imageName;
            }
            if ($request->hasFile('idback')) {
                $imageName = 'IDB'.time().'.'.$request->idback->extension();  
                $request->idback->move(public_path('drivers'), $imageName);
                $appuser->idback = 'drivers/'.$imageName;
            }
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Regiter'); 
            $credentials = $request->only('mobile', 'password');
            if (Auth::guard('api')->attempt($credentials)) {
                $user = Auth::guard('api')->user();
                    if($token=GenerateApiToken($user)){
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
