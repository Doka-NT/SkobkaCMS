<?php
class Imageui {
    public function Init(){
        Event::Bind('CacheDelete','Imageui::EventCacheDelete');
    }
    public function Rules(){
        return array('Настройка ImageUI');
    }
    
    public function Menu(){
        return array(
            'admin/imageui'=>array(
                'title'=>'Группы обработки изображений',
                'rules'=>array('Настройка ImageUI'),
                'callback'=>'ImageUIPages::Settings',
                'file'=>'ImageUIPages',
                'group'=>'Оформление'
            ),
            'admin/imageui/edit'=>array(
                'rules'=>array('Настройка ImageUI'),
                'callback'=>'ImageUIPages::EditPreset',
                'file'=>'ImageUIPages',
                'type'=>'callback',
            ),
            'image'=>array(
                'rules'=>array('Обычный доступ'),
                'type'=>'callback',
                'file'=>'ImageUIProcess',
                'callback'=>'ImageUIProcess::Get'
            ),
            STATIC_DIR .'/imageui'=>array(
                'callback'=>'ImageUIProcess::RewriteStatic',
                'file'=>'ImageUIProcess',
                'type'=>'callback',
            )
        );
    }
    
    public static function PresetList(){
        global $pdo;
        $q = $pdo->q("SELECT * FROM imageui");
        $list = array();
        while($preset = $pdo->fo($q)){
            $list[] = $preset;
        }
        return $list;
    }
    
    public static function GetUrl($preset_id,$file){
        return Path::Url(STATIC_DIR . DS . 'imageui/'.$preset_id.'/'.$file);
    }
    
    public static function prepareFile($filename){
        return str_replace('/','-',$filename);
    }
    
    public static function GetImage($preset_id,$file,$alt = 'image'){
        $url = ImageUI::GetUrl($preset_id,$file);
        $url2 = Path::UrlAbs($url);
        return '<img src="'.$url.'" alt="'.$alt.'" '.Image::HtmlSize($url2) .' />';
    }
    
    public static function EventCacheDelete(){
        $path = STATIC_DIR . DS . 'imageui';
        foreach(glob($path . DS . '/*') as $el){
            File::RmDir($el);
        }
    }
}