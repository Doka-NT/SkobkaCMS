<?php
require_once('include/core.class.php');     
$oEngine = new Core();
set_error_handler('Notice::PhpError');
Event::Call('Cron');
Path::Replace('/');