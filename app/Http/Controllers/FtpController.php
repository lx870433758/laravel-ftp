<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FtpController extends Controller
{
    public function get_rawlist(Request $request){
        $dir = $request->input("dir") ? $request->input("dir") : "/";
        try{
            $data = $this->itemize_dir(ftp_rawlist($request->conn,$dir));
            if($data){
                if(isset($data[0]) && $data[0]['name'] == '.')
                    unset($data[0]);
                if(isset($data[1]) && $data[1]['name'] == '..')
                    unset($data[1]);
            }
            array_multisort(array_column($data, 'type'),SORT_FLAG_CASE, SORT_DESC, $data);
            ftp_close($request->conn);
            return response()->json(['status'=>200,'data'=> ['list'=> $data,'dir'=> $dir]]);
        }catch (\Exception $e){
            return response()->json(['status'=>402,'message'=> "请求失败"]);
        }
    }
    public function add_dir(Request $request){
        $dir = $request->input("dir");
        $new_dir = $request->input("new_dir");
        if(!$dir || !$new_dir){
            return response()->json(['status'=>402,'message'=> "缺少参数"]);
        }
        try{
            ftp_mkdir($request->conn,$new_dir);
            ftp_chdir($request->conn,$dir);
            ftp_close($request->conn);
            return response()->json(['status'=>200,'message'=> "请求成功"]);
        }catch (\Exception $e){
            return response()->json(['status'=>402,'message'=> "请求失败"]);
        }

    }
    public function remove(Request $request){
        $dir = $request->input("dir");
        $type = $request->input("type");
        if(!$dir || (!$type && $type !=0)){
            return response()->json(['status'=>402,'message'=> "缺少参数"]);
        }
        try{
            switch ($type){
                case "1":
                    ftp_rmdir($request->conn,$dir);
                    break;
                case "0":
                    ftp_rmdir($request->conn,$dir);
                    break;
                default:
                    return response()->json(['status'=>402,'message'=> "参数错误"]);
            }
            return response()->json(['status'=>200,'message'=> "请求成功"]);
        }catch (\Exception $e){
            return response()->json(['status'=>402,'message'=> "参数错误"]);
        }

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
