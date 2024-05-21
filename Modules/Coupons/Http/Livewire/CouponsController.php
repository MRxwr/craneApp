<?php

namespace Modules\Coupons\Http\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Modules\Coupons\Entities\Coupon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class CouponsController extends Controller
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
        return view('coupons::livewire.coupon.add');
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
            
        ]);

        $page = new Coupon();
        
            $page->title = $request->title;
            $page->coupon_code = $request->coupon_code;
            $page->coupon_type = $request->coupon_type;
            $page->coupon_value = $request->coupon_value;
            $page->expiry_date = $request->expiry_date;
            $page->save();
            return redirect()->back()->with('success', 'Coupon created successfully!');
       
    
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
        $service = Coupon::findOrFail($id);
        return view('coupons::livewire.coupon.edit', compact('service'));
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
            
        ]);
        //dd($request->all());
        $page = Coupon::findOrFail($id);
        if( $page){
            $page->title = $request->title;
            $page->coupon_code = $request->coupon_code;
            $page->coupon_type = $request->coupon_type;
            $page->coupon_value = $request->coupon_value;
            $page->expiry_date = $request->expiry_date;
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
