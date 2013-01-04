<?php

class ImageUIProcess {

    public static function Get() {
        /*$preset_id = Path::Arg(1);
        $path = STATIC_DIR . DS . 'imageui' . DS . $preset_id . DS;
        $args = Path::Arg();
        unset($args[0], $args[1]);
        $file = implode(DS, $args);
        if (!$file)
            return Menu::NotFound();
        $filename = Imageui::prepareFile($file);
        if (File::Exists($path . $filename))
            Core::Header('Location', Path::Url($path . $filename), 301);
        $image = Image::Resize($file, 100);
        Image::Crop($image, 100, 1000);
        Image::Save($image, $path . $filename);
        Core::Header('Location', Path::Url($path . $filename), 301);*/
    }

    public static function RewriteStatic() {
        $path = Path::Arg();
        unset($path[0], $path[1], $path[2]);
        $preset_id = Path::Arg(2);
        
        $filepath = implode(DS, $path);
        $filename_new = Imageui::prepareFile($filepath);
        $dir_path = STATIC_DIR . DS . 'imageui' . DS . $preset_id . DS;
        if (File::Exists($dir_path . $filename_new)){
            Core::Header('Location', Path::Url($dir_path . $filename_new),301);
            Core::Off();
        }
        if (File::Exists($filepath)) {
            $image = Image::Create($filepath);
            $preset = Imageui::Load($preset_id); 
            
            ob_start();
            eval('?>' . $preset->code);
            ob_end_clean();
            
            Image::Save($image, $dir_path . $filename_new);
            Core::Header('Location', Path::Url($dir_path . $filename_new),301);
            Core::Off();
        }
        return Menu::NotFound();
    }

}