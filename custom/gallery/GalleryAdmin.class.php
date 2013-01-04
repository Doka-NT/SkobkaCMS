<?php

class GalleryAdmin {
    public static function SettingsPage(){
        return Form::GetForm('GalleryAdmin::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id'=>'gallery-settings-form',
            'fields'=>array(
                Theme::Render('select','gallery_types[]','Типы материалов, к которым будет прикреплена галерея',array(-1=>'Ни одного') + Content::GetTypes(), Variable::Get('gallery_types'), array('multiple'=>'multiple')),
                //Theme::Render('input','text','gallery_ext','Допустимые типы файлов галереи',  Variable::Get('gallery_ext','jpg jpeg png')),
                Theme::Render('select','gallery_imageui','Выводить изображения как:',array(-1=>'Оригинальное') + Imageui::PresetList(true),  Variable::Get('gallery_imageui','-1')),
            ),
            'standart'=>true,
            'submit'=>array('GalleryAdmin::SettingsFormSubmit'),
        );
    }
    
    public static function SettingsFormSubmit(&$aResult){
        $types = $aResult['POST']['gallery_types'];
        $key = array_search('-1', $types);
        if($key !== false)
            unset($types[$key]);
        $attach_types = Variable::Get('attach_types',array());
        foreach($types as $type){
            if(array_search($type, $attach_types) === false)
                    $attach_types[] = $type;
        }
        Variable::Set('attach_types',$attach_types);
    }
}