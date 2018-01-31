<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        $ftp = ftp_connect("106.14.10.215",21);
        return 1;
    }
}
