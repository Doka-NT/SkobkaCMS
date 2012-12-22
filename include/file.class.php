<?php

class File {
    public static function Copy($file_src,$file_dst){
        if(!File::Exists($file_src))
            return false;
        $dir = File::FileDir($file_dst);
        File::CreateDir($dir);
        return copy($file_src,$file_dst);
    }
    
    public static function Move($file_src,$file_dst){
        if(!File::Exists($file_src))
            return false;
        $dir = File::FileDir($file_dst);
        File::CreateDir($dir);
        $res = copy($file_src,$file_dst);
        $res2 = File::Delete($file_src);
        return $res && $res2;
    }
    
    public static function Exists($file){
        return file_exists($file);
    }
    
    public static function FileDir($filepath){
        $info = pathinfo($filepath);
        return $info['dirname'];
    }
    
    public static function Filename($filepath){
        return basename($filepath);
    }
    
    public static function CreateDir($path){
        if(!is_dir($path))
            return mkdir($path,0777,true);
        return true;
    }
    
    public static function Delete($filepath){
        return unlink($filepath);
    }
    
    public static function Ext($filepath){
        $info = pathinfo($filepath);
        return $info['extension'];
    }
}