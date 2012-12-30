<?php
class ImageUIPages {
    public static function Settings(){
        return Form::GetForm('ImageUIPages::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id'=>'imageui-settings-form',
            'type'=>'template',
            'template'=>Module::GetPath('imageui') . DS . 'forms' . DS . 'settings-form.tpl.php',
            'submit'=>array('ImageUIPages::SettingsFormSubmit'),
            'form-actions'=>array(
                'submit'=>array('text'=>'Добавить набор'),
            ),
        );
    }
    
    public static function SettingsFormSubmit(&$aResult){
        global $pdo;
        $pdo->insert('imageui',array(
           'title' =>  $_POST['preset']['title'],
        ));
        Notice::Message('Набор добавлен. Необходимо добавить действия к данному набору.');
    }
    
    public static function EditPreset(){
        
    }
}