<?php

class CustomCss {

    public function Menu() {
        return array(
            'admin/customcss' => array(
                'title' => 'Добавить/Редактировать CSS',
                'rules' => array('Редактировать CSS'),
                'callback' => 'CustomCss::MainPage',
            ),
        );
    }
    
    public function Init(){
        Event::Bind('LoadHeadInfo','CustomCss::EventHeadInfo');
        
    }
    
    public static function EventHeadInfo(&$data){
        $css_data = Variable::Get('cusstom-css-data', '');
        $data .= '<style type="text/css">'.$css_data.'</style>';
    }
    
    public static function MainPage() {
        return Form::GetForm('CustomCss::MainForm');
    }

    public static function MainForm() {
        return array(
            'id' => 'cusstomcss-main-form',
            'type' => 'callback',
            'callback' => 'CustomCss::MainFormCallback',
            'standart' => TRUE,
        );
    }

    public static function MainFormCallback() {
        $css_data = Variable::Get('cusstom-css-data', '');
        return Theme::Render('input', 'textarea', 'cusstom-css-data','Пользовательские стили', $css_data)
        . Editor::LoadCode('cusstom-css-data','css');
    }

}