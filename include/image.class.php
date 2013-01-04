<?php

class Image {

    public static function Create($filepath) {
        switch (File::Ext($filepath)) {
            case 'png': $image = imagecreatefrompng($filepath);
                break;
            case 'gif': $image = imagecreatefromgif($filepath);
                break;
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($filepath);
                break;
        }
        if ($image)
            Image::SaveAlpha($image);
        return $image;
    }

    public static function GetType($filepath) {
        switch (File::Ext($filepath)) {
            case 'png': $type = 'png';
                break;
            case 'gif': $type = 'gif';
                break;
            case 'jpg':
            case 'jpeg':
                $type = 'jpeg';
                break;
        }
        return $type;
    }

    public static function SaveAlpha(&$image) {
        ImageAlphaBlending($image, false);
        imagesavealpha($image, true);
    }

    public static function Build($image) {
        if (is_string($image))
            $image = Image::Create($image);
        Image::SaveAlpha($image);
        return $image;
    }

    public static function Width($image) {
        if (is_string($image)) {
            $size = getimagesize($image);
            return $size[0];
        }
        return imagesx($image);
    }

    public static function Height($image) {
        if (is_string($image)) {
            $size = getimagesize($image);
            return $size[y];
        }
        return imagesy($image);
    }

    public static function Resize($image, $w, $h = false) {
        $image = Image::Build($image);
        $iw = Image::Width($image);
        $ih = Image::Height($image);
        if (!$h) {
            $ratio = $w / $iw;
            $h = $ih * $ratio;
        }
        $tmp_image = Image::Tmp($w, $h);
        imagecopyresampled($tmp_image, $image, 0, 0, 0, 0, $w, $h, $iw, $ih);
        imagedestroy($image);
        return $tmp_image;
    }

    public static function Crop($image, $w, $h) {
        $image = Image::Build($image);
        $iw = Image::Width($image);
        $ih = Image::Height($image);
        $w = $w < $iw ? $w : $iw;
        $h = $h < $ih ? $h : $ih;
        $tmp_image = Image::Tmp($w, $h);
        imagecopy($tmp_image, $image, 0, 0, 0, 0, $w, $h);
        imagedestroy($image);
        return $tmp_image;
    }

    public static function ResizeCrop($image, $w, $h) {
        $image = Image::Build($image);
        $image = Image::Resize($image, $w);
        $image = Image::Crop($image, $w, $h);
        return $image;
    }

    public static function Save($image, $filepath) {
        $dir = File::FileDir($filepath);
        File::CreateDir($dir);
        $type = Image::GetType($filepath);
        $func = 'image' . $type;
        if ($type == 'jpeg')
            return $func($image, $filepath, 100);
        if ($type == 'png')
            return $func($image, $filepath, 0);
        if ($type == 'gif')
            return $func($image, $filepath);
    }

    public static function Tmp($w, $h) {
        $image = imagecreatetruecolor($w, $h);
        Image::SaveAlpha($image);
        return $image;
    }

    public static function HtmlSize($filepath) {
        $size = getimagesize($filepath);
        if ($size)
            return $size[3];
    }

}