<?php

use App\Models\User;
use App\Models\Locale;
use App\Models\Language;
use App\Models\Setting;
use App\Services\FCMService;
use Modules\Roles\Entities\Role;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\AppUserMeta;
use Modules\AppUser\Entities\Wallet;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

//AppUserMeta

function updateStatus(Model $model, $id)
{
    $dt = $model::find($id);
    if ($dt->is_active == 1) {
        $dt->is_active = 0;
        $dt->save();
    } else {
        $dt->is_active = 1;
        $dt->save();
    }
}

function my_ids()
{
    return Auth::id();
}

function akses($str)
{
    $my_id = my_ids();
    $role_id = User::find($my_id)->role_id;
    // dd($role_id);
    $role = Role::find($role_id);
    // dd($role);
    $permissions = $role->permissions;
    $permissions = json_decode($permissions);

    if (in_array($str, $permissions)) {
        return true;
    } else {
        return false;
    }
}
function _lang($slug){
    $token = Request::header('Authorization');
    $code=  (Session::get('locale')?Session::get('locale'):'en');
    $lang = Locale::where('slug',$slug)->first();
   if($lang ){
     return $lang->locales[$code];
   }else{
      return str_replace("_"," ",$slug);
   }
}
function getActiveLanguages(){
  return  $languages= Language::where('status',1)->get();
}

function getLocale(){
   return $lang = (Session::get('locale')? Session::get('locale'):'en');
}
function getSetting($slug){
   $locale = (Session::get('locale')? Session::get('locale'):'en');
   $setting = Setting::find(1);
   if($slug=='sitetitle'){
     return $setting->sitetitle[$locale];
   }elseif($slug=='sitedesc'){
    return $setting->sitedesc[$locale];
   }elseif($slug=='logo'){
    return $setting->logo;
   }elseif($slug=='favicon'){
    return $setting->favicon;
   }else{
    return $setting->$slug;
   }
}
function LanguagesDropdown(){
    $html='';
    $lang = (Session::get('locale')?Session::get('locale'):'en');
    if(getActiveLanguages()){
        $html .='<!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                '.$lang.'  
                </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">';
               foreach(getActiveLanguages() as $language){
                  $html .='<a href="'.asset('language/'.$language->code).'" class="dropdown-item"> '._lang($language->title).'</a>';
                }
         $html .='</div>
        </li>';
    }
    return $html;
}
function outputSuccess($data){
	$response["ok"] = true;
	$response["error"] = "0";
	$response["status"] = "successful";
	$response["data"] = $data;
	return response()->json($response, 200);
}

function outputError($data){
	$response["ok"] = false;
	$response["error"] = "1";
	$response["status"] = "Error";
	$response["data"] = $data;
    return response()->json($response, 200);
}
function sendSMS($msg){
    $response["ok"] = true;
	$response["error"] = "0";
	$response["status"] = "successful";
	$response["data"] = '';
    return response()->json($response, 200);
}
function GenerateApiToken($user){
    if($user){
        // Generate a token for the user
        $token = Str::random(60);
        $user->token = hash('sha256', $token);
        $user->save();
        if($user->save()){
            return $token;
        }  
    }
}   
function getHashToken($token){  
        if($token){
          return  hash('sha256', $token);
        }  
}
function getAllUserMeta($key,$app_user_id){
    $usermeta= AppUserMeta::where('app_user_id',$app_user_id)->get();
    if($usermeta){
        $metas=[];
        foreach($usermeta as $meta){
            $metas[$meta->key] = $meta->value;
        }
        return $metas;
      }
} 
function getUserMeta($key,$app_user_id){
    $usermeta= AppUserMeta::where('key',$key)->where('app_user_id',$app_user_id)->first();
    if($usermeta){
        return $usermeta->value;
      }
} 
function upadteUserMeta($key,$value,$app_user_id){
   $usermeta= AppUserMeta::where('key',$key)->where('app_user_id',$app_user_id)->first();
   if($usermeta){
     $usermeta->value = $value;
     $usermeta->save();
   }else{
    $usermeta = new AppUserMeta();
    $usermeta->app_user_id = $app_user_id;
    $usermeta->key = $key;
    $usermeta->value = $value;
    $usermeta->save();
   }
 } 
 function getPage($id=0){
    if($id>0){
       return Page::find($id)->toArray();
    }
  }
  function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
    // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
       return $angle * $earthRadius;
  }

  function AddBookingLog($bid,$activity){
    $log = new  BookingLog();
    $log->request_id = $bid->id;
    $log->client_id  = $bid->client_id;
    $log->driver_id  = ($bid->driver_id>0?$bid->driver_id:0);
    $log->activity   = $activity;
    $log->flag =1; 
    $log->save();

  }

  function firebaseNotification($user){

  }
  function walletTransaction($data){
    $wallet = new Wallet();
    $wallet->request_id=$data['request_id'];
    $wallet->app_user_id=$data['app_user_id'];
    $wallet->amount=$data['amount'];
    $wallet->mode=$data['mode'];
    $wallet->remark=$data['remark'];
    $wallet->save();
  }
