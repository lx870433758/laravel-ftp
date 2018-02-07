<?php

namespace App\Http\Middleware;

use Closure;

class FtpAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->input('info')){
            $param = json_decode(base64_decode($request->input('info')),true);
            $ip = $param['ip'];
            $port = $param['port'];
            $user_name = $param['user_name'];
            $password = $param['password'];
        }else{
            $ip = $request->input('ip');
            $port = $request->input('port');
            $user_name = $request->input('user_name');
            $password = $request->input('password');
        }

        $conn = ftp_connect($ip,$port);
        if(!$conn){
            return response()->json(['status'=>401,"message"=>"登录失败"]);
        }
        try{
            ftp_login($conn,$user_name,$password);
        }catch (\Exception $e){
            return response()->json(['status'=>401,"message"=>"登录失败"]);
        }
        $request->offsetSet('conn', $conn);
        return $next($request);
    }
}
