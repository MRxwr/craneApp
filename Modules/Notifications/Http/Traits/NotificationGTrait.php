<?php
namespace Modules\Notifications\Http\Traits;

use Modules\Notifications\Entities\GeneralNotification;

trait NotificationGTrait
{
    public static function firstForm()
    {
        $a['title'] = '';
        $a['text'] = '';
        $a['is_read'] = '';

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
        } elseif (!$data['text']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'description Required'
            ];
        } else {

            if ($id_edit) {
                $cek = GeneralNotification::where('title', $data['title'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'title already exist.'
                    ];
                }
            } else {
                $cek = GeneralNotification::where('title', $data['title'])->exists();

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
            GeneralNotification::find($id)->update($data);
        } else {
            GeneralNotification::create($data);
        }
    }

    public static function destroy($id)
    {
        $page= GeneralNotification::find($id);
        $page->is_deleted = 1;
        $page->save();
    }

    public static function find_data($id)
    {
        $dt = GeneralNotification::find($id);
        return [
            'title' => $dt->title,
            'text' => $dt->text,
            
        ];
    }
}
