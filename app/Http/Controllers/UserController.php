<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    
    public function register()
    {
        $user = new User();
        return view('user.register', compact('user'));
    }

    public function users()
    {
        
    }
}
