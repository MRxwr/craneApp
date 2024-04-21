<?php

namespace App\Traits;

use App\Models\Language;

trait LanguageTrait
{
    public static function firstForm()
    {
        $a['code'] = '';
        $a['title'] = '';
        return $a;
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        if (!$data['title']) {
            // $this->emit('pesanGagal', 'Name Required');
            return [
                'success' => false,
                'message' => 'Title Required'
            ];
        } elseif (!$data['code']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'Code Required'
            ];
        } else {

            if ($id_edit) {
                $cek = Language::where('code', $data['code'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'Maaf email sudah digunakan..'
                    ];
                }
            } else {
                $cek = Language::where('code', $data['code'])->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'Maaf email sudah digunakan..'
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
            Language::find($id)->update($data);
        } else {
            Language::create($data);
        }
    }

    public static function destroy($id)
    {
        Language::find($id)->delete();
    }

    public static function find_data($id)
    {
        $dt = Language::find($id);
         return [
            'code' => $dt->code,
            'title' => $dt->title, 
            'iso_code' => $dt->iso_code, 
            'status' => $dt->status, 
        ];
    }
}
