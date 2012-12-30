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
        $preset = Imageui::Load(Path::Arg(3));
        Theme::SetTitle($preset->title);
        return Form::GetForm('ImageUIPages::PresetForm',$preset);
    }
    
    public static function PresetForm(){
        return array(
            'id'=>'imageui-preset-form',
            'type'=>'template',
            'template'=>Module::GetPath('imageui') . DS . 'forms' . DS . 'imageui-preset-form.tpl.php',
            'submit'=>array('ImageUIPages::PresetFormSubmit'),
            'form-actions'=>array(
                'submit'=>array('text'=>'Сохранить')
            )
        );
    }
    
    public static function PresetFormSubmit(&$aResult){
        global $pdo;
        $preset = $aResult[1];
        $code = $_POST['preset_code'];
        $pdo->q("UPDATE imageui SET code = ? WHERE id = ?",array(
            $pdo->serialize($code),
            $preset->id,
        ));
        Notice::Message('Настройки сохранены');
    }
}