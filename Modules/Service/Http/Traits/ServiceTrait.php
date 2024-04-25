<?php
namespace Modules\Service\Http\Traits;

use Modules\Service\Entities\Service;

trait ServiceTrait
{
    public static function firstForm()
    {
        $a['title'] = '';
        $a['description'] = '';
        $a['image'] = '';

        return $a;
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        if (!$data['title']) {
            // $this->emit('pesanGagal', 'Name Required');
            return [
                'success' => false,
                'message' => 'title Required'
            ];
        } elseif (!$data['description']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'description Required'
            ];
        } else {

            if ($id_edit) {
                $cek = Service::where('title', $data['title'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'title already exist.'
                    ];
                }
            } else {
                $cek = Service::where('title', $data['title'])->exists();

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
            Service::find($id)->update($data);
        } else {
            Service::create($data);
        }
    }

    public static function destroy($id)
    {
        $service= Service::find($id);
        $service->is_deleted = 0;
        $service->save();
    }

    public static function find_data($id)
    {
        $dt = Service::find($id);

        return [
            'title' => $dt->title,
            'description' => $dt->description,
            'image' => $dt->image
        ];
    }
}
