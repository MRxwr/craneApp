<?php

namespace Modules\FAQs\Http\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Modules\FAQs\Entities\Faq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FAQsController extends Controller
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
        return view('faqs::livewire.faq.add');
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
        ]);

        $faq = new Faq();
        
            $faq->title = $request->title;
            $faq->description = $request->description;
            $faq->save();
            return redirect()->back()->with('success', 'FAQs created successfully!');
       
    
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
        $service = Faq::findOrFail($id);
        return view('faqs::livewire.faq.edit', compact('service'));
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
           
        ]);
        //dd($request->all());
        $faq = Faq::findOrFail($id);
        if( $faq){
            $faq->title = $request->title;
            $faq->description = $request->description;
            $faq->save();
            return redirect()->back()->with('success', 'FAQ Saved successfully!');
        }else{
            return redirect()->back()->with('error', 'FAQ Not exist!');
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
