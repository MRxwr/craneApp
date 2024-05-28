<?php
namespace Modules\Notifications\Http\Traits;

use Modules\Notifications\Entities\Notification;

trait NotificationTrait
{
    public static function firstForm()
    {
        $a['token'] = '';
        $a['text'] = '';
        $a['is_read'] = '';

        return $a;
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        
    }

    public static function store_data($data, $id = null)
    {
        // dd($data);
        if ($id) {
            Notification::find($id)->update($data);
        } else {
            Notification::create($data);
        }
    }

    public static function destroy($id)
    {
        $page= Notification::find($id);
        $page->is_deleted = 1;
        $page->save();
    }

    public static function find_data($id)
    {
        $dt = Notification::find($id);

        return [
            'token' => $dt->title,
            'text' => $dt->text,
            
        ];
    }
}
