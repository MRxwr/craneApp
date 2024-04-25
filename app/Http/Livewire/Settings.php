<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Language;
use App\Models\Setting;

class Settings extends Component
{
    use WithFileUploads;
    public $row;
    public $logo;
    public $favicon;
    
    protected $rules = [
        'row.*' => 'required|string|max:255',
        // Add validation rules for other properties as needed
    ];

    // public $file;
    // public $description;
    public function mount($rowId)
    {
        $this->row = Setting::find($rowId);
    }

   
   
    public function update()
    {
        // Validation logic if needed

        
        $this->validate();
        if ($this->logo) {
            $this->row->logo_path = $this->logo->store('logos');
        }

        if ($this->favicon) {
            $this->row->favicon_path = $this->favicon->store('favicons');
        }

        $this->row->save();

        // Redirect or emit event if needed
    }

    public function render()
    {
        $languages = Language::where('status',1)->get();
        return view('livewire.setting', compact(
            'languages'
        ))
            ->layout('layouts.main', [
                'title' =>_lang('Manage settings') 
            ]);
    }
}
