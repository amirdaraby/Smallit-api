<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register (Request $request){
        $fields = $request->validate([
            "name"=>"required|string|min:2|mix:255",
            "email"=>"required|string|email|unique:User,email",
            "password"=>"required|string|confirmed"
        ]);
    }
}
