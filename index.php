<?php
if(!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configuration.php')){
	define("INSTALL",true,true);
	include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install.php';
	exit;
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Content-type: text/html; charset=utf-8");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(E_ALL ^E_NOTICE);

ini_set('arg_separator.output',     '&amp;');
ini_set('magic_quotes_gpc',			0);
ini_set('magic_quotes_runtime',     0);
ini_set('magic_quotes_sybase',      0);
ini_set('session.cache_expire',     200000);
ini_set('session.cache_limiter',    'none');
ini_set('session.cookie_lifetime',  2000000);
ini_set('session.gc_maxlifetime',   200000);
//ini_set('session.save_handler',     'user');
ini_set('session.use_cookies',      1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid',    0);
ini_set('url_rewriter.tags',        '');

require 'include/core.class.php';

$oEngine = new Core();
set_error_handler('Notice::PhpError');
/*Trigger boot event*/
Event::Call('boot');

/*Trigger MenuExecute event*/

$path = Path::Get();
Event::Call('MenuExecute',$path);
global $sContent;
Event::Call('PagePreRender');
echo Theme::Page($sContent);