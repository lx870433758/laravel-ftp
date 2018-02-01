<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $ip = $request->input('ip');
        $port = $request->input('port');
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        $ip = "106.14.10.215";
        $port = 21;
        $user_name="lx";
        $password="lx123456";
        $conn = ftp_connect($ip,$port);
        if(!$conn){
            return response()->json(['status'=>401,"message"=>"登录失败"]);
        }

        try{
            ftp_login($conn,$user_name,$password);
        }catch (\Exception $e){
            return response()->json(['status'=>401,"message"=>"登录失败"]);
        }

       return (ftp_rawlist($conn,"."));

        ftp_close($conn);
        return 1;
    }
}
