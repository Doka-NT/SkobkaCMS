<?php

class Lightbox {
    public $aRules = array('Настраивать Lightbox');
    
    public $aMenu = array(
        'admin/settings/lightbox'=>array(
            'rules'=>array('Настраивать Lightbox'),
            'title'=>'Настройка Lightbox',
            'callback'=>'Lightbox::Settings',
            'group'=>'Настройки'
        ),
    );
    
    public $EventFullLoaded = 'Lightbox::EventFullLoaded';
    
    public function EventFullLoaded() {
        $all_pages = Variable::Get('lightbox_allpages',true);
        if($all_pages)
            Lightbox::Load();
    }

    public static function Load() {
        $lib = 'colorbox';
        Theme::AddJsSettings(array(
            'lightbox' => array(
                'selector' => Variable::Get('lightbox_selector', 'a.lightbox'),
            ),
        ));
        Lightbox::LoadLib($lib);
    }

    public static function LoadLib($lib) {
        if ($lib == 'colorbox') {
            $path = Module::GetPath('Lightbox') . DS . 'lib' . DS . 'colorbox';
            Theme::AddJs($path . DS . 'jquery.colorbox-min.js');
            Theme::AddJs($path . DS . 'init.js');
            Theme::AddCss($path . DS . 'colorbox.css');
        }
    }

    public static function Settings(){
        return Form::GetForm('Lightbox::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id' => 'lightbox-settings-form',
            'standart' => TRUE,
            'sisyphus' => FALSE,
            'fields' => array(
                Theme::Render('select','lightbox_allpages','Включить на всех страницах', array('0'=>'Нет','1'=>'Да'), Variable::Get('lightbox_allpages',1)),
                Theme::Render('input','text','lightbox_selector','jQuery селектор для эффекта',Variable::Get('lightbox_selector', 'a.lightbox')),
            ),
        );
    }
}