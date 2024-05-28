<?php

namespace Modules\Notifications\Http\Livewire\Notifications;

use Modules\Notifications\Entities\Notification;
use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Notifications\Http\Traits\NotificationTrait;
use Modules\Roles\Http\Traits\PermissionTrait;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit,$image;

    protected $listeners =['fileUpload'=>'handleFileUpload'];

    public function handleFileUpload ($imageData){
        
        $this->image = $imageData;
    }
    public function mount()
    {
        $this->paging = 25;
        $this->forms = NotificationTrait::firstForm();
        // dd($this->forms);

    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($key, $val)
    {
        // dd($key . ' | ' . $val);
        // dd($this->forms);
    }

    public function update_status($id)
    {
        if (!akses('change-status-user')) {
            $this->emit('pesanGagal', 'Access Denied..');
            return false;
        }

        try {
            $dt = Notification::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            updateStatus(new Notification, $id);

            $this->emit('pesanSukses', 'Sucess..');
            // }
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);

            $this->emit('pesanGagal', $pesan);
        }
    }

    public function tambah_data()
    {
        $this->reset(['is_edit', 'id_edit']);
        $this->forms = NotificationTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = NotificationTrait::find_data($id);

        $this->emit('modalAdd', 'show');
    }

    public function store()
    {
        $this->validate([
            'forms.*' => 'required'
        ]);
        try {

           
            // dd($this->forms);
            if ($this->id_edit) {
                $validasi = NotificationTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = NotificationTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {
                    NotificationTrait::store_data($this->forms, $this->id_edit);
                } else {
                    
                    NotificationTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');
                $this->forms = NotificationTrait::firstForm();
                $this->emit('pesanSukses', 'Store Success..');
                $this->reset(['is_edit', 'id_edit']);
            }
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);
            $this->emit('pesanGagal', $pesan);
        }
    }

    public function destroy($id)
    {
        try {
            NotificationTrait::destroy($id);
            $this->emit('pesanSukses', 'Success..');
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);
            $this->emit('pesanGagal', $pesan);
        }
    }

    public function render()
    {
        $q = $this->search;
        $data = Notification::where('is_deleted',0)->filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
        return view('notifications::livewire.notification.index', compact(
            'data',
            'pagings',
        ))->layout('layouts.main', [
                'title' => _lang('Manage System Notifications')
        ]);
    }
}
