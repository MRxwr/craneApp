<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function Success()
    {
        return view('page');
    }
    public function Failed()
    {
        return view('page');
    }

    
}
