<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register (Request $request){
        $validate = $this->validate($request,[
           "name"=> "required",
            "email"=> "required",
            "password"=>"required"
        ]);


    }
}
