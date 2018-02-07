/**
 * Created by admin on 2018/2/5.
 */
export default class func{
    getLastDir(dir){
        if(dir=='/'){
            return "/"
        }
        var a = dir.lastIndexOf('/');
        if(a == '-1'){
            return "/"
        }
        if(a+1 == dir.length){
            dir = dir.substr(0,dir.length -1);
            a = dir.lastIndexOf('/');
        }
        return dir.substr(0,a+1);
    }
}