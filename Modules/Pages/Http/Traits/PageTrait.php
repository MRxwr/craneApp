<?php
namespace Modules\Pages\Http\Traits;

use Modules\Pages\Entities\Page;

trait PageTrait
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
                $cek = Page::where('title', $data['title'])->exists();

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
            Page::find($id)->update($data);
        } else {
            Page::create($data);
        }
    }

    public static function destroy($id)
    {
        $page= Page::find($id);
        $page->is_deleted = 1;
        $page->save();
    }

    public static function find_data($id)
    {
        $dt = Page::find($id);

        return [
            'title' => $dt->title,
            'description' => $dt->description,
            'image' => $dt->image
        ];
    }
}
