<?php
namespace Modules\AppUser\Http\Traits;

use Modules\AppUser\Entities\AppUser;

trait AppUserTrait
{
    public static function firstForm()
    {
        $a['name'] = '';
        $a['email'] = '';
        $a['mobile'] = '';
        $a['dob'] = '';
        $a['is_active'] = '';
        $a['user_type'] = '';
        $a['avator'] = '';
        return $a;
    }

    public static function clientsForms(){
        // Retrieve forms based on the user_type
        $userForms = AppUser::where('user_type', 1)->where('is_deleted', 0)->get();

        // If forms are found, return them
        if ($userForms->isNotEmpty()) {
            return $userForms->map(function ($form) {
                return [
                    'name' => $form->name,
                    'email' => $form->email,
                    'mobile' => $form->mobile,
                    'dob' => $form->dob,
                    'is_active' => $form->is_active,
                    'user_type' => $form->user_type,
                    'avator' => $form->avator
                ];
            });
        }

        // If no forms are found, return an empty array
        return [];
    }

    public static function driversForms(){
        // Retrieve forms based on the user_type
        $userForms = AppUser::where('user_type', 2)->where('is_deleted', 0)->get();

        // If forms are found, return them
        if ($userForms->isNotEmpty()) {
            return $userForms->map(function ($form) {
                return [
                    'name' => $form->name,
                    'email' => $form->email,
                    'mobile' => $form->mobile,
                    'dob' => $form->dob,
                    'is_active' => $form->is_active,
                    'user_type' => $form->user_type,
                    'avator' => $form->avator
                ];
            });
        }

        // If no forms are found, return an empty array
        return [];
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        if (!$data['name']) {
            // $this->emit('pesanGagal', 'Name Required');
            return [
                'success' => false,
                'message' => 'Name Required'
            ];
        } elseif (!$data['email']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'email Required'
            ];
        } elseif (!$data['mobile']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'mobile Required'
            ];
        }else {

            if ($id_edit) {
               
                if (AppUser::where('email', $data['email'])->where('id', '!=', $id_edit)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'This email already exist..'
                    ];
                }
                if (AppUser::where('mobile', $data['mobile'])->where('id', '!=', $id_edit)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'This email already exist..'
                    ];
                }
            } else {
            
                if (AppUser::where('email', $data['email'])->exists()) {
                    return [
                        'success' => false,
                        'message' => 'This email already exist..'
                    ];
                }
                if (AppUser::where('mobile', $data['mobile'])->exists()) {
                    return [
                        'success' => false,
                        'message' => 'This mobile already exist..'
                    ];
                }
            }



            return [
                'success' => true,
                'message' => 'Success..'
            ];
        }
    }

    public static function store_data($data, $id = null)
    {
        // dd($data);
        if ($id) {
            AppUser::find($id)->update($data);
        } else {
            AppUser::create($data);
        }
    }

    public static function destroy($id)
    {
       $user= AppUser::find($id);
       $user->is_deleted = 1;
       $user->save();
    }

    public static function find_data($id)
    {
        $dt = AppUser::find($id);

        return [
            'name' => $dt->name,
            'mobile' => $dt->mobile,
            'email' => $dt->email,
            'dob' => $dt->dob,
            'is_active' => $dt->is_active,
            'user_type' => $dt->user_type,
            'avator' => $dt->avator
        ];
    }
}
