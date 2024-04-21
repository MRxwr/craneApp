<?php

use App\Models\User;
use App\Models\Locale;
use App\Models\Language;
use Modules\Roles\Entities\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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
    $code=app()->getLocale();
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
   return app()->getLocale();
}
