<?php
error_reporting(E_ALL ^E_NOTICE);
if(!defined("INSTALL")){
	header("Location: /",true,301);
	exit;
}
session_start();
function query($sql){
	global $pdo;
	return $pdo->prepare($sql)->execute();
}

function set_error($message){
	exit('<div class="alert alert-danger">'.$message.'</div><a href="javascript:window.history.back(-1);" class="btn btn-danger">Вернуться</a>');
}

function check_param($bool){
	return $bool?'<span class="yes">Да</span>':'<span class="no">Нет</span>';
}
function get_mode_rewrite(){
	return $_SERVER['HTTP_MOD_REWRITE'] == 'On'?true:false;
}
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
		<title>Установка Skobka.CMS</title>
		<link rel="stylesheet" type="text/css" href="./themes/default/bootstrap/css/bootstrap.min.css"/>
		<style type="text/css">
			body {background:#393939;}
			#main-page {width:960px;margin:50px auto 0 auto;padding: 20px;border: 1px solid #EEE;border-radius: 5px;background: #fff;box-shadow: 5px 5px 5px;}
			#form {overflow:hidden;}
			#form fieldset {width:500px;float:left;}
			#req {width:400px; padding:20px; float:right; border-left:1px solid #E5E5E5}
			#form .form-actions {clear:both;text-align:right;}
			.req-item {margin-bottom: 5px;}
			.status {font-weight:normal;float:right;}
			.yes {color: #29B929;}
			.no {color:red;}
		</style>
	</head>
	<body>
		<div id="main-page">
			<h1>Установка Skobka.CMS</h1>
			<form action="" method="post" id="form">
				<div id="req">
					<h4>Проверка необходимых требований</h4>
					<div class="req-item">Расширение php PDO:<span class="status"><?=check_param(class_exists('PDO'));?></span></div>
					<div class="req-item">Файл <i>configuration.php</i> доступен для записи:<span class="status"><?=check_param((is_writeable(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configuration.php'))||(is_writeable(dirname(__FILE__))));?></span></div>
					<div class="req-item">Версия PHP 5.3 и выше:<span class="status"><?=check_param(PHP_VERSION_ID >= 50300);?></span></div>
					<div class="req-item">Короткие теги:<span class="status"><?=check_param(ini_get('short_open_tag'));?></div>
					<div class="req-item">Apache mod_rewrite:<span class="status"><?=check_param(get_mode_rewrite());?></div>
					<div class="alert">Внимание! Если один из пунктов указывает о проблеме, то установка может завершиться неудачей!</div>
				</div>			
				<fieldset>
					<?if(!$_POST['step']): // STEP 1?>
					<legend>Шаг 1. Установка параметров.</legend>
					
					<label class="form-inline">Сервер базы данных MySQL</label>
					<input type="text" name="p[db_server]" value="localhost"/>

					<label>Имя базы данных</label>
					<input type="text" name="p[db_name]" />
					
					<label>Пользователь БД</label>
					<input type="text" name="p[db_user]" />

					<label>Пароль для БД</label>
					<input type="text" name="db_password" />
					
					<input type="hidden" name="step" value="1" />
					<?elseif($_POST['step'] == 1)://STEP 2?>
						<?
							$bool = true;
							foreach($_POST['p'] as $v)
								if(!$v)
									$bool = false;
							$DB = array(
								'host'=>$_POST['p']['db_server'],
								'dbname'=>$_POST['p']['db_name'],
								'user'=>$_POST['p']['db_user'],
								'password'=>$_POST['db_password'],
							);
						?>
						<?if($bool):?>
						<?
							try {
								$pdo = new PDO("mysql:host={$DB['host']};dbname={$DB['dbname']};charset=utf8", $DB['user'], $DB['password']); 
								$_SESSION['DB'] = $DB;
								$GLOBALS['pdo'] = $pdo;
								
								
								
								/*IMPORT TABLE blocks */
								query("
CREATE TABLE IF NOT EXISTS `blocks` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `pages` varchar(255) NOT NULL,
  `not_pages` text NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`bid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");								
								/*IMPORT TABLE blocks_custom */
								query("
CREATE TABLE IF NOT EXISTS `blocks_custom` (
  `bcid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`bcid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE content */
								query("
CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  `data` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE content_type */
								query("
CREATE TABLE IF NOT EXISTS `content_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE forms */
								query("
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(255) NOT NULL,
  `form_hash` varchar(255) NOT NULL,
  `validate` longtext NOT NULL,
  `submit` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE menu */
								query("
CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE menu_items */
								query("
CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`menu_item_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE modules */
								query("
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE session */
								query("
CREATE TABLE IF NOT EXISTS `session` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE url_alias */
								query("
CREATE TABLE IF NOT EXISTS `url_alias` (
  `uaid` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`uaid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE users */
								query("
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
								/*IMPORT TABLE variables */
								query("
CREATE TABLE IF NOT EXISTS `variables` (
  `var_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`var_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	

								/*IMPORT TABLE user_groups */
								query("
CREATE TABLE IF NOT EXISTS `user_groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rules` longtext NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");	

								/*IMPORT TABLE updates*/
								query("
CREATE TABLE  `updates` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`module` VARCHAR( 255 ) NOT NULL ,
`version` INT NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;								    
");
									
							} catch (PDOException $e){
								echo '<div class="alert alert-danger">'.$e->getMessage().'</div><a href="javascript:window.history.back(-1);" class="btn btn-danger">Вернуться</a>';exit;
							}
						?>
						<legend>Шаг 2 - Настройка пользователя</legend>
						
						<label>Логин суперпользователя</label>
						<input type="text" name="p[name]" />
						
						<label>Email</label>
						<input type="text" name="p[email]" />
						
						<label>Пароль</label>
						<input type="text" name="p[password]" />
						
						<input type="hidden" name="step" value="2"/>
						<?else:?>
							<div class="alert alert-danger">Необходимо заполнить поля <b>Сервер, Имя БД, Пользователь</b></div>
							<a href="/" class="btn btn-danger">Вернуться назад</a>
						<?endif;?>
					<?elseif($_POST['step'] == 2)://STEP 3?>
						<?
							foreach($_POST['p'] as $v)
								if(!$v)
									exit('<div class="alert alert-danger">Необходимо заполнить все поля</div><a href="javascript:window.history.back(-1);" class="btn btn-danger">Вернуться</a>');
							try {
								$DB = $_SESSION['DB'];
								$pdo = new PDO("mysql:host={$DB['host']};dbname={$DB['dbname']};charset=utf8", $DB['user'], $DB['password']); 
								query("TRUNCATE `blocks`;");
								query("TRUNCATE `users`;");
								query("
INSERT INTO `users` (`uid`, `gid`, `name`, `mail`, `password`) 
VALUES
(1, 2, '".$_POST['p']['name']."', '".$_POST['p']['email']."', MD5('".$_POST['p']['password']."'));
");
								query("
INSERT INTO `blocks` (`bid`, `block_id`, `position`, `pages`, `weight`) VALUES
(1, 'block-side-menu', 'absolute', '', 5);
");
								query("
INSERT INTO `variables` (`var_id`, `name`, `value`) VALUES
(1, 'site_theme', 'czo5OiJza29ia2FjbXMiOw==')");                
								query("
INSERT INTO `user_groups` (`gid`, `name`, `rules`) VALUES
(1, 'Гости', 'YToxOntpOjA7czoyNzoi0J7QsdGL0YfQvdGL0Lkg0LTQvtGB0YLRg9C/Ijt9'),
(2, 'Авторизованные', 'YToxOntpOjA7czoyNzoi0J7QsdGL0YfQvdGL0Lkg0LTQvtGB0YLRg9C/Ijt9');");
                                                                
							}
							catch (PDOException $e){
								set_error($e->getMessage());
							}
							
							try {
								$conf = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'default.configuration.php');
								$DB = $_SESSION['DB'];
								$conf = str_replace(array(
									'db_server','db_name','db_user','db_password'
								),$DB,$conf);
								file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configuration.php',$conf);

                                                                /*START UPDATES*/
                                                                require_once('include/core.class.php');     
                                                                $oEngine = new Core();                                                                
                                                                $version = CMS_VERSION * 100;
                                                                Event::Call('Update',$version);
                                                                Variable::Set('ActualDbVersion', $version + 1);	
								
								$pdo->query("UPDATE blocks SET theme = '*' WHERE bid = 1",array());
							}catch(Exception $e){
								set_error($e->getMessage());
							}
						?>
						<legend>Шаг 3 - Готово!</legend>
						<p>Skobka.CMS установлена!</p>
						<p>
							<a href="/" class="btn btn-success">Перейти на главную</a>	<a href="/user" class="btn btn-success">Перейти на сраницу авторизации</a>
						</p>
						<?exit;?>
					<?endif;?>
				</fieldset>
				<div class="form-actions">
					<input type="submit" value="Далее" class="btn btn-success"/>
				</div>
			</form>
		</div>
	</body>
</html>