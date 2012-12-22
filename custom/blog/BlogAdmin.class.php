<?php

class BlogAdmin {
    public static function SettingsPage(){
        return Form::GetForm('BlogAdmin::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id'=>'blog-settings-form',
            'standart'=>TRUE,
            'type'=>'template',
            'template'=>Module::GetPath('blog') . DS . 'forms' . DS . 'blog-settings-form.tpl.php',
            
        );
    }
}