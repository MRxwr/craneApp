<?php

namespace Modules\Service\Http\Livewire\Service;

use Modules\Service\Entities\Service;
use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Service\Http\Traits\ServiceTrait;
use Modules\Roles\Http\Traits\PermissionTrait;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit,$image,$imageUrl;

    public function mount()
    {
        $this->paging = 25;
        $this->forms = ServiceTrait::firstForm();
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
            $dt = Service::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            updateStatus(new Service, $id);

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
        $this->forms = ServiceTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = ServiceTrait::find_data($id);

        $this->emit('modalAdd', 'show');
    }

    public function store()
    {
        $this->validate([
            'forms.*' => 'required'
        ]);
        try {

            if ($this->image) {
                $originalName = $this->image->getClientOriginalName();
                $mimeType = $this->image->getMimeType();
                $imageName =   $this->image->store('uploads', 'public');
                // Get the URL for the uploaded image
                $this->imageUrl = asset($imageName);
                $this->forms['image_path']=$this->imageUrl;
                $this->image = null;
            }
            dd($this->forms);
            if ($this->id_edit) {
                $validasi = ServiceTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = ServiceTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {
                    ServiceTrait::store_data($this->forms, $this->id_edit);
                } else {
                    ServiceTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');

                $this->forms = ServiceTrait::firstForm();
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
            ServiceTrait::destroy($id);
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
        $data = Service::filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
       
        return view('service::livewire.service.index', compact(
            'data',
            'pagings',
        ))
            ->layout('layouts.main', [
                'title' => 'Manage Service'
            ]);
    }
}
