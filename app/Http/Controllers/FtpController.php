<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FtpController extends Controller
{
    //获取目录列表详细
    public function get_rawlist(Request $request)
    {
        $dir = $request->input("dir") ? $request->input("dir") : "/";
        try {
            $data = $this->itemize_dir(ftp_rawlist($request->conn, $dir));
            if ($data) {
                if (isset($data[0]) && $data[0]['name'] == '.')
                    unset($data[0]);
                if (isset($data[1]) && $data[1]['name'] == '..')
                    unset($data[1]);
            }
            array_multisort(array_column($data, 'type'), SORT_FLAG_CASE, SORT_DESC, $data);
            ftp_close($request->conn);
            return response()->json(['status' => 200, 'data' => ['list' => $data, 'dir' => $dir]]);
        } catch (\Exception $e) {
            return response()->json(['status' => 402, 'message' => "请求失败"]);
        }
    }

    //获取添加目录
    public function add_dir(Request $request)
    {
        $dir = $request->input("dir");
        $new_dir = $request->input("new_dir");
        if (!$dir || !$new_dir) {
            return response()->json(['status' => 402, 'message' => "缺少参数"]);
        }
        try {
            ftp_chdir($request->conn, $dir);
            ftp_mkdir($request->conn, $new_dir);
            ftp_close($request->conn);
            return response()->json(['status' => 200, 'message' => "请求成功"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 402, 'message' => "请求失败"]);
        }

    }

    //新建文件
    public function create_file(Request $request)
    {
        try {
            $dir = $request->input('dir');
            $file_name = $request->input('file_name');
            $local_file = $request->input('local_file');
            if (!$dir || !$file_name) {
                return response()->json(['status' => 402, 'message' => "缺少参数"]);
            }
            $myfile = $local_file ? fopen($local_file, "a+") : fopen(storage_path().DIRECTORY_SEPARATOR."update".DIRECTORY_SEPARATOR."new_file.txt", "a+");
            ftp_chdir($request->conn, $dir);
            ftp_nb_fput($request->conn, $file_name, $myfile, FTP_ASCII, 0);
            ftp_close($request->conn);
            return response()->json(['status' => 200, 'message' => "请求成功"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 402, 'message' => "请求失败"]);
        }

    }

    //删除目录或文件
    public function remove(Request $request)
    {
        $dir = $request->input("dir");
        $type = $request->input("type");
        if (!$dir || (!$type && $type != 0)) {
            return response()->json(['status' => 402, 'message' => "缺少参数"]);
        }
        switch ($type) {
            case "1":
                $res = $this->remove_dir($request->conn, $dir);
                break;
            case "0":
                ftp_delete($request->conn, $dir);
                $res = 200;
                break;
            default:
                $res = 402;
        }
        if ($res != 200) {
            return response()->json(['status' => 402, 'message' => "删除失败"]);
        }
        return response()->json(['status' => 200, 'message' => "请求成功"]);
    }

    //目录详情字符串转数组
    function itemize_dir($contents)
    {
        $dir_list = [];
        $tmp_array = [];
        foreach ($contents as $file) {
            if (preg_match("/([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)/", $file, $regs)) {
                $type = (int)strpos("-dl", $regs[1][0]);
                $tmp_array['type'] = $type;
                $tmp_array['permissions'] = $regs[1];
                $tmp_array['user'] = $regs[3];
                $tmp_array['size'] = $regs[5];
                $tmp_array['date'] = date("Y-m-d H:i:s", strtotime($regs[6] . " " . $regs[7]));
                $tmp_array['name'] = $regs[9];
            }
            $dir_list[] = $tmp_array;
        }
        return $dir_list;
    }

    //递归删除目录
    public function remove_dir($conn, $dir)
    {
        $dir_list = $this->itemize_dir(ftp_rawlist($conn, $dir));
        if (!$dir_list) {
            return 402;
        }
        foreach ($dir_list as $k => $v) {
            if ($v['name'] != "." && $v['name'] != "..") {
                if ($v['type'] == 0) {
                    ftp_delete($conn, $dir . '/' . $v['name']);
                } else {
                    $this->remove_dir($conn, $dir . '/' . $v['name']);
                }
            }
        }
        ftp_rmdir($conn, $dir);
        return 200;
    }

    public function rename(Request $request)
    {
        $dir = $request->input('dir');
        $from = $request->input('from');
        $to = $request->input('to');
        if (!$from || !$to) {
            return response()->json(['status' => 200, 'message' => "请求成功"]);
        }
        try {
            ftp_chdir($request->conn, $dir);
            ftp_rename($request->conn, $from, $to);
            ftp_close($request->conn);
            return response()->json(['status' => 200, 'message' => "请求成功"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 402, 'message' => "请求失败"]);
        }
    }

    public function down_file(Request $request){
        set_time_limit(0);
        $info = $request->input('info');
        $param = json_decode(base64_decode($info),true);
        if(!isset($param['dir']) || !isset($param['file_name'])){
            return response()->json(['status' => 402, 'message' => "缺少参数"]);
        }
        $local = storage_path().DIRECTORY_SEPARATOR."update".DIRECTORY_SEPARATOR.$param['file_name'];
        ftp_chdir($request->conn, $param['dir']);
        $suffix = substr(strrchr($param['file_name'],'.'),1);
        if($suffix == "zip" || $suffix == "rar" || $suffix == "jpg"|| $suffix == "png"){
            ftp_fget($request->conn,fopen($local, "w+"),$param['file_name'],FTP_BINARY,0);
        }else{
            ftp_fget($request->conn,fopen($local, "w+"),$param['file_name'],FTP_ASCII,0);
        }
        if (!file_exists($local)) {
            return response()->json(['status' => 402, 'message' => "请求失败"]);
        }
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: " . filesize($local));
        header("Content-Disposition: attachment; filename=" . $param['file_name']);
        echo fread(fopen($local,"r+"), filesize($local) ? filesize($local) : 1);
        ftp_close($request->conn);
        unlink($local);
        return "";
    }

    public function upload_file(Request $request){
        $dir = $request->input("dir");
        ftp_chdir($request->conn, $dir);
        //ftp_put($request->conn,"target.txt","source.txt",FTP_ASCII);
        //move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
        ftp_nb_fput($request->conn,$_FILES["file"]["name"],fopen($_FILES["file"]["tmp_name"], "a+"),FTP_BINARY,0);
        ftp_close($request->conn);
        return 1;
    }
}
