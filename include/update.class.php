<?php

class Update {
    public static function Save($module,$version){
	global $pdo;
	$pdo->query("DELETE FROM updates WHERE module = ?");
	$pdo->insert("updates",array(
	    'module'=>$module,
	    'version'=>$version,
	));
    }
    
    public static function GetModuleUpdatedVersion($module){
	global $pdo;
	$res = $pdo->QR("SELECT * FROM updates WHERE module LIKE ?",array($module));
	return $res->version;
    }
}