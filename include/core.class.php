<?php
define('CUSTOM_PATH','custom/',TRUE);
//define('DS', DIRECTORY_SEPARATOR, true);
define('DS', '/', true);
define('STATIC_DIR','static',true);
class Core {
	public $modules;
	
	
	public function __construct(){
                $GLOBALS['memory_start'] = memory_get_usage();
		$this->magic_quotes_off();
		$this->LoadFiles();
                
		$GLOBALS['pdo'] = new DBPDO();
                
                Path::_Preload();
                Variable::_Preload();
                
		$this->modules = $this->LoadModules();
                
                
                
		$GLOBALS['session'] = User::AuthCookie();
		$this->modules += $this->LoadCustomModules();
		$this->modules = (object)$this->modules;
		//unset($GLOBALS['_ENV'],$GLOBALS['HTTP_ENV_VARS']);
		$GLOBALS['oEngine'] = $this;
		$this->menu = new Menu();
		$GLOBALS['oEngine'] = $this;
		$this->LoadSettings();
		
		$this->CallInit();		
		$theme = new Theme();
                
                
		$GLOBALS['oEngine']->theme_stack = $theme->Stack();
		Event::Call('Loaded');
	}
	
	/*PRIVATE*/
	/*Load system classes*/
	private function LoadFiles(){
		$aFiles = array(
			'event',
			'pdo',
			'module',
			'notice',
			'path',
			'menu',			
			'theme',
			'form',
			'variable',
			'session',
                        'file',
                        'image',
		);
		foreach($aFiles as $sFile)
			require 'include/' . $sFile . '.class.php'; 
	}
	
	private function LoadModules(){
		$aFiles = array(
			'admin','user','content','block','nav','filemanager','editor','jqueryui','mail',
		);
		$aModules = array();
		foreach($aFiles as $sFile) {
			require 'modules/' . $sFile . '/Module.class.php';
			$aModules[$sFile] = new $sFile();
		}
		
			//if(method_exists($sFile,'Init'))
				//$aModules[$sFile]->Init();		
		return $aModules;
	}
	
	/*Load system settings*/
	private function LoadSettings(){
		
		$GLOBALS['theme'] = $_GET['minimal']?Variable::Get('site_minimal_theme','default'):Variable::Get('site_theme','default');
		$GLOBALS['theme_info'] = Theme::ThemeInfo();
		$GLOBALS['site_name'] = 'Тестовый сайт';
		$GLOBALS['web_root'] = '/';
                $GLOBALS['root_host'] = 'http://' . $_SERVER['SERVER_NAME'] . $GLOBALS['web_root'];
		//$GLOBALS['session'] = session_name();
		session_start();
	}
	/*PROTECTED*/
	
	/*PUBLIC*/
	public static function GetSalt(){
		return $GLOBALS['salt'];
	}
	
	private function magic_quotes_off(){
		if (get_magic_quotes_gpc()) {
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
			while (list($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					} else {
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
			unset($process);
		}	
	}
	
	private static function LoadCustomModules(){
		global $pdo;
		$q = $pdo->query("SELECT * FROM modules WHERE status = 1");
		$aModules = array();
		while($module = $pdo->fetch_object($q)){
			$file = CUSTOM_PATH . $module->name . DS . 'module.class.php';
			if(file_exists($file)){
				include $file;
				$aModules[$module->name] = new $module->name();
			}
		}
		return $aModules;
	}
	
	private function CallInit(){
		foreach($this->modules as &$module)
			if(method_exists($module,'Init'))
				$module->Init();
	}
	
	public static function LoadModule($module){
		$file = Module::GetPath($module) . DS . 'Module.class.php';
		if(!file_exists($file))
			return false;
		
		include_once $file;
		$oModule = new $module();
		if(method_exists($oModule,'Init'))
			$oModule->Init();
		return $oModule;
	}
	
	public static function Json($data){
		if(!is_array($data))
			$data = array('data'=>$data);
		if(!$data['status'])
			$data['status'] = true;
		if(!$data['message'])
			$data['message'] = base64_encode(Notice::GetAll());
		
		exit(json_encode($data));
	}
}