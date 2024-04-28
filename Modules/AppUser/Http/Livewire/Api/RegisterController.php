<?php
namespace Modules\AppUser\Http\Livewire\Api;


use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    
    public function register(Request $request){
        $data = array();
        $mobileNumber = $request->input('mobile_number');
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
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Mobile not found');
            return outputSuccess($data);
        }else{
            $appuser = new AppUser;
            $appuser->name = $name;
            $appuser->email = $email;
            $appuser->mobile = $mobileNumber;
            $appuser->dob = $dob;
            $appuser->password = bcrypt($request->password);
            $appuser->email_verified_at = now();
            $appuser->save();
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Regiter'); 
        }
        if (auth()->attempt($mobile, $password)) {
            return outputSuccess($data);
        } else {
            $data['message']=_lang('login faild Regiter');
            return outputError($data);
        }

    }
}
