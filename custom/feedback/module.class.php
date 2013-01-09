<?php

class feedback {

    public $aMenu = array(
        'feedback' => array(
            'title' => 'Обратная связь',
            'callback' => 'feedback::Page',
        ),
    );
    
    public static function Page(){
        return Form::GetForm('feedback::MainForm');
    }
    
    public static function MainForm(){
        return array(
            'id'=>'feedback-form',
            'fields'=>array(
                Theme::Render('input','text','name','Как Вас зовут?'),
                Theme::Render('input','text','contact','Телефон или эл.почта'),
                Theme::Render('input','textarea','message','Сообщение'),
            ),
            'required'=>array(
                'name','contact','message'
            ),
            'form-actions'=>array(
                'submit'=>array('text'=>'Отправить'),
            ),
           
            'submit'=>array('feedback::MainFormSubmit'),
        );
    }
    
    public static function MainFormSubmit(&$aResult){
        //Отправлять будет на почту администратору, а его uid = 1
        $admin = User::Load(1);
        $to = $admin->mail;
        $site_name = Variable::Get('site_name','');
        $subject = 'Обратная связь - '.$site_name;
        
        array_map(function($el){
            return htmlspecialchars(strip_tags($el));
        },$aResult['POST']);
        
        $body = 'На сайте '.$site_name.' было отправлено сообщение используя форму обратной связи.';
        $body .= '<br><br>';
        $body .= '<b>Как Вас зовут:</b> '.$aResult['POST']['name'] .'<br/>';
        $body .= '<b>Телефон или эл.почта:</b> '.$aResult['POST']['contact'] .'<br/>';
        $body .= '<b>Сообщение:</b><br/>'.$aResult['POST']['message'];
        
        Mail::Send('feedback-mail', $to, $subject, $body);
        Notice::Message('Спасибо! Сообщение отправлено.');
        Path::Replace('frontpage');
    }
}