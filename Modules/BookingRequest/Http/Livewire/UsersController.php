<?php

namespace Modules\AppUser\Http\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
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
        return view('appuser::livewire.users.add');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string', // Additional validation (e.g., regex) might be needed
            'email' => 'required|email|string|max:255',
            'dob' => 'required|date', // Adjust date format if needed (e.g., date_format:Y-m-d)
            'password' => 'nullable|string|max:255|alpha_num',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
       
        
        $user = new AppUser();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->user_type = $request->user_type;
        $user->password = Hash::make($request->password); // bcrypt hashing
  
        if ($request->hasFile('avator')) {
            $imageName = time().'.'.$request->avator->extension();  
            $request->avator->move(public_path('avators'), $imageName);
            $user->avator = 'storage/avators/'.$imageName;
        }
        $user->save();
        return redirect()->back()->with('success', 'User Saved successfully!');
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
        $user = AppUser::findOrFail($id);
        return view('appuser::livewire.users.edit', compact('user'));
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
            'name' => 'required|string|max:255',
            'mobile' => 'required|string', // Additional validation (e.g., regex) might be needed
            'email' => 'required|email|string|max:255',
            'dob' => 'required|date', // Adjust date format if needed (e.g., date_format:Y-m-d)
            'password' => 'nullable|string|max:255|alpha_num',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        //dd($request->all());
        $user = AppUser::findOrFail($id);
        if( $user){
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->dob = $request->dob;
            $user->user_type = $request->user_type;
            if($request->password){
                $user->password = Hash::make($request->password); // bcrypt hashing
            }
            
            if ($request->hasFile('avator')) {
                $imageName = 'img-'.time().'.'.$request->avator->extension();
               // Save the file to the 'public' disk
                $request->avator->storeAs('avators', $imageName, 'public');
                $user->avator = 'storage/avators/'.$imageName;
            }
            $user->save();
            return redirect()->back()->with('success', 'User Saved successfully!');
        }else{
            return redirect()->back()->with('error', 'User Not exist!');
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
