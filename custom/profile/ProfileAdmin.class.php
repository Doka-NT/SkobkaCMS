<?php

class ProfileAdmin {
    public static function Settings(){
        return Form::GetForm('ProfileAdmin::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id'=>'profile-settings-form',
            'standart'=>TRUE,
            'type'=>'template',
            'template'=>Module::GetPath('profile') . DS . 'forms' . DS . 'profile-settings-form.tpl.php',
        );
    }
}