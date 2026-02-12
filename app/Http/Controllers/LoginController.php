<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Show the application Login.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function indexLogin()
    {
        return view('auth.login');
    }   
}
