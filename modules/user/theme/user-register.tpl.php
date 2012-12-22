<?php
    Theme::AddJs(Module::GetPath('user') . DS . 'js' . DS . 'user-register.js');    
?>
<?=Theme::Render('input','text','name','Имя пользователя (логин)');?>
<?=Theme::Render('input','text','mail','Эл. почта');?>
<?=Theme::Render('input','password','password','Пароль');?>
<?=Theme::Render('input','password','password_2','Повторите пароль');?>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>'Зарегистрироваться'),
));?>