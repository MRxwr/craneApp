<?php

use App\Models\User;
use App\Models\Locale;
use App\Models\Language;
use App\Models\Setting;
use App\Services\FCMService;
use Modules\Roles\Entities\Role;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\AppUserMeta;
use Modules\AppUser\Entities\AppUserRating;
use Modules\AppUser\Entities\Wallets;
use Modules\AppUser\Entities\LoginAttempt;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\BookingPayment;
use Modules\AppUser\Entities\AppUserActivity;
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
    if($token){
        $token = str_replace('Bearer ', '', $token);
        $user = AppUser::where('token',$token)->first();
        if($user){
            $code= $user->language?$user->language:'ar'; 
        }else{
            $code= 'ar';   
        } 
    }else{
        $code =  (Session::get('locale')?Session::get('locale'):'en');
    }
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
function sendSMS($msg,$mobile,$flag){
    $message = urlencode($msg);
    $sms_username = getSetting('sms_username');
    $sms_password = getSetting('sms_password');
    $sms_sender = getSetting('sms_senderid');
	$message = str_replace(' ','+',$message);
    $mobile = '+'.$mobile;
    if($flag==0){
        $url = 'http://www.kwtsms.com/API/send/?username='.$sms_username.'&password='.$sms_password.'&sender='.$sms_sender.'&mobile='.$mobile.'&lang=1&message='.$message;
           $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err){
                return $err;
            }else{
                return $response;	
            }
        $flag=1;
    }	

   
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
      }else{
        return false;
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
  function firebaseNotification($user_id,$title,$message='',$data=[]){
   
    try {
        if($user_id){
            $user = AppUser::find($user_id);
           if($user){ 
              // off firebase  notification by comment code below line
              // if need to send notification by firebase use below code
               $firebaseNotificationService = app(FCMService::class);
               $response = $firebaseNotificationService->sendNotification($user->device_token,$title,$message,$data);
             }
      }
        
    } catch (\Exception $e) {
        return ['error' => 'Failed to send notification', 'details' => $e->getMessage()];
    }

  }
  function walletTransaction($data){
    $wallet = new Wallets();
    $wallet->request_id=$data['request_id'];
    $wallet->app_user_id=$data['app_user_id'];
    $wallet->amount=$data['amount'];
    $wallet->mode=$data['mode'];
    $wallet->remark=$data['remark'];
    $wallet->save();
  }
  function checkCoupon($price){
    
    if(Request::input('is_coupon') && Request::input('is_coupon')=='yes'){
        $discount = Request::input('discount');
        return $price = $price - $discount;
    }else{
        return $price; 
    }
  }

  function DoBooking($dt,$transaction_id,$payment_type,$price,$remark){
    $booking =  new BookingPayment();
    $booking->request_id =$dt->id;
    $booking->client_id =$dt->client_id;
    $booking->transaction_id =$transaction_id;
    $booking->payment_type =$payment_type;
    if($payment_type=='wallet'){
        $booking->payment_status ='success';
    }else{
        $booking->payment_status ='ongoing';
    }
    
    if(Request::input('is_coupon') && Request::input('is_coupon')=='yes'){
        $booking->is_coupon='yes';
        $booking->coupon_code=Request::input('coupon_code');
        $booking->coupon_code=Request::input('coupon_discount');
     }
    $booking->payment_amount =$price;
    $booking->remark =$remark;
    if($booking->save()){
        return true;
    }
  }

function getUserRating($user_id){
   $ratings = AppUserRating ::where('rating_user_id',$user_id)->get();
   $totalRating = 0;
   if($ratings->count()>0){
     $numberofrating =$ratings->count();
     $totalRating = 0;
     foreach($ratings as $key=>$rate){
        $totalRating = $totalRating + floatval($rate->rating);
     }
     $totalRating = $totalRating/$numberofrating;
   }
   return number_format($totalRating, 1);
}
function addUserActivity($user_id,$request_id=0,$activity,$flag){
 
    if($user_id>0){
        $activt = new AppUserActivity;
        $activt->app_user_id =$user_id;
        $activt->request_id =$request_id;
        $activt->activity =$activity;
        $activt->flag =$flag;
        $activt->save();
    }
}
function AppUserLogingStatus($user_id){
    $completedLoginAttempts = LoginAttempt::where('app_user_id', $user_id)
                    ->whereNull('end_time')
                    ->get();
    if($completedLoginAttempts->count()>0) {
        return 1;
    } else{
        return 0;
    }              
}
if (!function_exists('formatDateWithOrdinal')) {
    function formatDateWithOrdinal($date)
    {
        if ($date !== null) {
            // Get the day, month, hour, and minute
            $day = $date->format('j'); // Day of the month without leading zeros
            $month = $date->format('M'); // Short month name (e.g., 'Sep')
            $hour = $date->format('g'); // 12-hour format without leading zeros
            $minute = $date->format('i'); // Minutes
            $ampm = $date->format('A'); // AM or PM

            // Add the ordinal suffix
            $dayWithSuffix = $day . getOrdinalSuffix($day);

            // Format the final output
            return "{$dayWithSuffix} {$month} {$hour}:{$minute}{$ampm}";
        }
        return 'Date not available';
    }

    function getOrdinalSuffix($day)
    {
        if (in_array($day % 10, [1, 2, 3]) && !in_array($day % 100, [11, 12, 13])) {
            return ['st', 'nd', 'rd'][$day % 10 - 1];
        }
        return 'th';
    }
}
