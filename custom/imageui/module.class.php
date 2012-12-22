<?php
class Imageui {
    public function Rules(){
        return array('Настройка ImageUI');
    }
    
    public function Menu(){
        return array(
            'admin/imageui'=>array(
                'title'=>'Список настроек',
                'rules'=>array('Настройка ImageUI'),
                'callback'=>'ImageUIPages::Settings',
                'file'=>'ImageUIPages',
            ),
        );
    }
}