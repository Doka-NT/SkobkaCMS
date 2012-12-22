<?php

class VK_Comments {

    public static function Rules() {
        return array(
            'Настраивать соц.комментарии',
        );
    }

    public function Init() {
        Event::Bind('ContentView','VK_Comments::EventContentView');
    }

    public function Menu() {
        return array(
            'admin/vk_comments' => array(
                'title' => 'Настройка',
                'rules' => array('Настраивать соц.комментарии'),
                'callback' => 'VK_Comments::SettingsPage',
            ),
        );
    }

    public static function SettingsPage() {
        return Form::GetForm('VK_Comments::SettingsForm');
    }

    public static function SettingsForm() {
        return array(
            'id' => 'vk-comments-settings-form',
            'type' => 'callback',
            'callback' => 'VK_Comments::SettingsFormBuilder',
            'standart' => true,
        );
    }

    public static function SettingsFormBuilder() {
        
        return array(
            Theme::Render('input', 'textarea', 'vk_comments_code', 'Код', Variable::Get('vk_comments_code', '')),
            Editor::LoadCode('vk_comments_code'),
            '<p>Код можно получить <a href="http://vk.com/developers.php?oid=-1&p=Comments" target="_blank">на этой странице</a>.',
            Theme::Render('select','vk_comments_types[]','Типы материала для добавления комментариев',Content::GetTypes(),Variable::Get('vk_comments_types',array()),array('multiple'=>'multiple','style'=>'height:100px'))
        );
    }

    public static function EventContentView(&$arg){
        $types = Variable::Get('vk_comments_types',array());
        if(in_array($arg['content']->type,$types))
                $arg['content']->data .= self::Load ();
    }
    
    public static function Load(){
        $code = Variable::Get('vk_comments_code','');
        return $code;
    }
}
