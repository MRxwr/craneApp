<?php

namespace Modules\Pages\Http\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Modules\Pages\Entities\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        //return view('service::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('service::livewire.service.add');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title.*' => 'required|string|max:255',
            'description.*' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $service = new Service();
        
            $service->title = $request->title;
            $service->description = $request->description;
            if ($request->hasFile('image')) {
                
                $imageName = 'img-'.time().'.'.$request->image->extension();
               // Save the file to the 'public' disk
                $request->image->storeAs('services', $imageName, 'public');
                $service->image = 'storage/services/'.$imageName;
            }
            $service->save();
            return redirect()->back()->with('success', 'Service created successfully!');
       
    
        // Redirect or return response as needed
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('service::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id){
        $service = Service::findOrFail($id);
        return view('service::livewire.service.edit', compact('service'));
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title.*' => 'required|string|max:255',
            'description.*' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        //dd($request->all());
        $service = Service::findOrFail($id);
        if( $service){
            $service->title = $request->title;
            $service->description = $request->description;
            if ($request->hasFile('image')) {
                
                $imageName = 'img-'.time().'.'.$request->image->extension();
               // Save the file to the 'public' disk
                $request->image->storeAs('services', $imageName, 'public');
                $service->image = 'storage/services/'.$imageName;
            }
            $service->save();
            return redirect()->back()->with('success', 'Service Saved successfully!');
        }else{
            return redirect()->back()->with('error', 'Service Not exist!');
        }
        
    
        // Redirect or return response as needed
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
