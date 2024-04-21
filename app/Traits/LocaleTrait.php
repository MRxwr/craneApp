<?php

namespace App\Traits;

use App\Models\Locale;

trait LocaleTrait
{
    public static function firstForm()
    {
        $a['slug'] = '';
        $a['locales'] = '';
        return $a;
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        if (!$data['slug']) {
            // $this->emit('pesanGagal', 'Name Required');
            return [
                'success' => false,
                'message' => 'Slug Required'
            ];
        } elseif (!$data['locales']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'locale  Required'
            ];
        } else {

            if ($id_edit) {
                $cek = Locale::where('slug', $data['slug'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'local successfuly changed..'
                    ];
                }
            } else {
                $cek = Locale::where('slug', $data['slug'])->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'local successfuly changed..'
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
       if ($id) {
            Locale::find($id)->update($data);
        } else {
            Locale::create($data);
        }
    }

    public static function destroy($id)
    {
        Locale::find($id)->delete();
    }

    public static function find_data($id)
    {
        $dt = Locale::find($id);

        return [
            'id' => $dt->id,
            'slug' => $dt->slug,
            'locales' => $dt->locales
        ];
    }
}
