<?php
require_once('include/core.class.php');     
$oEngine = new Core();
set_error_handler('Notice::PhpError');
/*Trigger update event*/
global $user;
if($user->uid != 1)
    Menu::NotFound ();
$version = CMS_VERSION * 100;
Event::Call('Update',$version);
Variable::Set('ActualDbVersion', $version + 1);
Event::Call('PagePreRender');
if($user->uid == 1)
    $sContent = '<p align="center"><h4>Обновление</h4></p>';
echo Theme::Page($sContent);