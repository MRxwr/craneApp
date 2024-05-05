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

   
   
    public function update(Request $request, $rowId)
    {
           $this->validate();
            $row = Setting::find($rowId);
            $row->sitetitle = $request->sitetitle;
            $row->sitedesc = $request->sitedesc;
            $row->adminlang = $request->adminlang;
            $row->frontlang = $request->frontlang;
            $row->contact = $request->contact;
            $row->email = $request->email;
            $row->address = $request->address;
            
            if ($request->hasFile('logo')) {
                $imageName = 'logo-'.time().'.'.$request->logo->extension();
               // Save the file to the 'public' disk
                $request->logo->storeAs('site', $imageName, 'public');
                $row->logo = 'storage/site/'.$imageName;
            }
            if ($request->hasFile('favicons')) {
                $imageName = 'favicons-'.time().'.'.$request->favicons->extension();
               // Save the file to the 'public' disk
                $request->favicons->storeAs('site', $imageName, 'public');
                $row->favicons = 'storage/site/'.$imageName;
            }
            $row->save();
            return redirect()->back()->with('success', 'Service created successfully!');
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
