<?php
class Notice {
	public static $error = array();
	public static $message = array();
	
	public static function PhpError($errno,$errstr,$errfile,$file_str,$backtrace = null){
		if($errno != E_NOTICE) {
		$message = '<b>Ошибка: '.$errstr.'</b> в файле <i>'.$errfile.':'.$file_str.'</i>';
		Notice::Error($message);
		}
	}
	
	public static function Error($message){
		self::$error[] = $message;
		$_SESSION['error'] = self::$error;
	}
	
	public static function Message($message){
		self::$message[] = $message;
		$_SESSION['message'] = self::$message;
	}
	
	public static function GetAll(){
		self::$message = $_SESSION['message'];
		self::$error = $_SESSION['error'];
		if(self::$error){
			$error = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'.implode('<br>',self::$error).'</div>';
			$_SESSION['error'] = array();
		}
		if(self::$message) {
			$message = '<div class="alert message"><button type="button" class="close" data-dismiss="alert">×</button>'.implode('<br>',self::$message).'</div>';
			$_SESSION['message'] = array();
		}
		return $error . $message;
	}
}