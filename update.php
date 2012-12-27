<?php
require_once('include/core.class.php');     
$oEngine = new Core();
set_error_handler('Notice::PhpError');
/*Trigger update event*/
$version = CMS_VERSION * 100;
Event::Call('Update',$version);
Variable::Set('ActualDbVersion', $version + 1);
Event::Call('PagePreRender');
$sContent = '<p align="center"><h4>Обновление</h4></p>';
echo Theme::Page($sContent);