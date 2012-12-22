<?php
class Session {
	public static function Set($aData){
		global $pdo;
		return $pdo->insert('session',$aData);		
	}
	
	public static function Get($sessid){
		global $pdo;
		return $pdo->fetch_object($pdo->query("SELECT * FROM session WHERE name LIKE ?",array($sessid)));
	}
	
	public static function Delete($sessid){
		global $pdo;
		$pdo->query("DELETE FROM session WHERE name LIKE ?",array($sessid));
	}
}