<?php

namespace Modules\Notifications\Http\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Modules\Notifications\Entities\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class NotificationsController extends Controller
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
        return view('notifications::livewire.notification.add');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
            
        ]);

        $page = new Notification();
        
            $page->title = $request->title;
            $page->text = $request->text;
            $page->save();
            return redirect()->back()->with('success', 'Notification created successfully!');
       
    
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
        $service = Notification::findOrFail($id);
        return view('notifications::livewire.notification.edit', compact('service'));
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
            'title' => 'required|string|max:255',
            'text' => 'required|string',
            
        ]);
        //dd($request->all());
        $page = Notification::findOrFail($id);
        if( $page){
            $page->app_user_id = $request->app_user_id;
            $page->title = $request->title;
            $page->text = $request->text;
            $page->save();
            return redirect()->back()->with('success', 'Page Saved successfully!');
        }else{
            return redirect()->back()->with('error', 'Page Not exist!');
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
