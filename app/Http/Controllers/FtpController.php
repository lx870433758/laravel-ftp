<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FtpController extends Controller
{
    public function index(Request $request){
        $data = $this->itemize_dir(ftp_rawlist($request->conn,"."));
        return response()->json(['status'=>200,'data'=> $data]);
    }

    function itemize_dir($contents) {
        $dir_list=[];
        $tmp_array = [];
        foreach ($contents as $file) {
            if(preg_match("/([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)/", $file, $regs)) {
                $type = (int) strpos("-dl", $regs[1][0]);
                $tmp_array['type'] = $type;
                $tmp_array['permissions'] = $regs[1];
                $tmp_array['user'] = $regs[3];
                $tmp_array['size'] = $regs[5];
                $tmp_array['date'] = date("Y-m-d H:i:s",strtotime($regs[6]." ".$regs[7]));

                $tmp_array['name'] = $regs[9];
            }
            $dir_list[] = $tmp_array;
        }
        return $dir_list;
    }
}
