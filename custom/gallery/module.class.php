<?php
/**
 * Основной класс модуль Галерея.
 * 
 */
class Gallery {
    /**
     * Список правил определяемых модулем
     * @var array 
     */
    public $aRules = array('Настраивать галерею', 'Видеть галлерею',);
    /**
     * Схема роутинга модуля
     * @var array 
     */
    public $aMenu = array(
        'admin/settings/gallery' => array(
            'title' => 'Настройка галлереи',
            'group' => 'Настройки',
            'callback' => 'GalleryAdmin::SettingsPage',
            'file'=>'GalleryAdmin',
            'rules' => array('Настраивать галерею'),
        ),
    );
    
    public $aBlocks = array(
        'gallery-block'=>array(
            'title'=>'Случайные фото',
            'content'=>'123',
        ),
    );
    /**
     * Система темизации реализуемая модулем
     * @var array
     */
    public $aTheme = array(
        'GalleryPhoto'=>array(
            'type'=>'template',
            'template'=>'gallery-photo.tpl.php',
            'arguments'=>array('file'=>NULL,'preset'=>-1,'group_id'=>'group'),
        ),
    );
    
    public $EventContentLoad    =   'GalleryEvent::ContentLoad';
    public $EventFormLoad       =   'GalleryEvent::FormLoad';
    
    public function Init(){
        Module::IncludeFile('Gallery', 'GalleryEvent.class.php');
    }
    
    public static function View($oContent){
        if(!User::Access('Видеть галлерею'))
            return;
        if(!is_array($oContent->attach))
            return;
        $out = '';
        Lightbox::Load();
        $preset = Variable::Get('gallery_imageui',-1);
        foreach($oContent->attach as $file){
            if(strpos($file->type, 'image') === false)
                continue;
            $out .= Theme::Render('GalleryPhoto',$file,$preset);
        }
        return $out;
    }
    
}