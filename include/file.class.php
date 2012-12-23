<?php

class File {

    public static function Copy($file_src, $file_dst) {
        if (!File::Exists($file_src))
            return false;
        $dir = File::FileDir($file_dst);
        File::CreateDir($dir);
        return copy($file_src, $file_dst);
    }

    public static function Move($file_src, $file_dst) {
        if (!File::Exists($file_src))
            return false;
        $dir = File::FileDir($file_dst);
        if(!File::CreateDir($dir)){
            Notice::Error('Не возможно создать папку одноименную файлу '.$dir);
            return false;
        }
        $res = copy($file_src, $file_dst);
        $res2 = File::Delete($file_src);
        return $res && $res2;
    }

    public static function Exists($file) {
        return file_exists($file);
    }

    public static function FileDir($filepath) {
        $info = pathinfo($filepath);
        return $info['dirname'];
    }

    public static function Filename($filepath) {
        return basename($filepath);
    }

    public static function CreateDir($path) {
        if(is_file($path))
            return false;
        if (!is_dir($path))
            return mkdir($path, 0777, true);
        return true;
    }

    public static function Delete($filepath) {
        if(File::Exists($filepath))
            return unlink($filepath);
    }

    public static function Ext($filepath) {
        $info = pathinfo($filepath);
        return $info['extension'];
    }

    public static function SaveUpload($name) {
        $file = $_FILES[$name];
        $new_files = array();
        if(!is_array($file))
            return false;
        foreach ($file['error'] as $index => $status) {
            if ($status == UPLOAD_ERR_OK) {
                $filename = 'f_' . md5(time() . $file['name'][$index]) . '.' . File::Ext($file['name'][$index]);
                $filepath = 'files' . DS . 'upload' . DS . $filename;
                if (File::Move($file['tmp_name'][$index], $filepath))
                    $new_files[$index] = (object) array(
                        'type' => $file['type'][$index],
                        'size' => $file['size'][$index],
                        'name' => $filename,
                        'filepath' => $filepath,
                        'original_name' => $file['name'][$index],
                    );
                else 
                    Notice::Error ('Не уадалось загрузить файл '. $file['name'][$index]);
            }
        }
        return $new_files;
    }

}