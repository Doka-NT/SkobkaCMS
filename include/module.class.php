<?php

class Module {
	public static function GetPath($sModule){
		$path = strtolower('modules' . DS . $sModule);
		if(!is_dir($path))
			$path = strtolower(CUSTOM_PATH . $sModule);
		return is_dir($path)?$path:false;
	}
	
	public static function IncludeFile($sModule,$sFile){
		include_once Module::GetPath($sModule) . DS . $sFile;
	}
	
	public static function GetCustom(){
		$aModule = array();
		foreach(glob(CUSTOM_PATH . '*') as $module)if(is_dir($module)){
			$module = str_replace(CUSTOM_PATH,'',$module);
			$info = Admin::ModuleInfo($module);
			if(!$info['group'])
				$info['group'] = 'Без группы';
			$info['module'] = $module;
			$aModule[$info['group']][] = $info;
		}	
		return $aModule;
	}
	
	public static function GetInfo($sModule,$as_object = false){
		$file = Module::GetPath($sModule) . DS . 'module.info.php';
		if(file_exists($file))
                        if($as_object)
                            return (object) include $file;
                        else
                            return include $file;
		return false;
	}
}