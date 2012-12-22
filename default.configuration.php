<?php
$GLOBALS['salt'] = 'x(MS&(&N#((#!@!@';
define('FM_SECURITY_KEY',md5($_SERVER['DOCUMENT_ROOT'] . 'x2m238x00m3202023x421'),true);
$DB = array(
	'host'		=>	'db_server',
	'dbname'	=>	'db_name',
	'user'		=>	'db_user',
	'password'	=>	'db_password',
);