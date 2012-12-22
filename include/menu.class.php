<?php

class Menu {
	public function __construct(){
		/*Bind a event to MenuExecute */
		Event::Bind('MenuExecute','Menu::Execute');
	}

	
	
	public static function Execute($sPath){
		global $oEngine;
		$aMenu = array();
		$suggestItem = null;
		
		$SIDE_MENU = array();
		$GLOBALS['query_path'] = $sPath;
		$sPath = Path::GetByAlias($sPath);
                
		$GLOBALS['path'] = $sPath;
		foreach($oEngine->modules as $oModule)
			if(method_exists($oModule,'Menu')){
				$aMenuTemp = $oModule->Menu();
				foreach($aMenuTemp as $path=>$aMenuItemInfo){
					$aMenuItemInfo += Menu::DefaultItem();
					$aMenuTemp[$path]['module'] = get_class($oModule);
						if(Path::PathMatch($sPath,$path))
							$suggestItem = $aMenuTemp[$path];
					if($aMenuItemInfo['type'] == 'STANDART')
						$SIDE_MENU[get_class($oModule)][] = array('path'=>$path,'title'=>$aMenuItemInfo['title']);
				}
				$aMenu += $aMenuTemp;
			}
		$GLOBALS['side_menu'] = $SIDE_MENU;
		$GLOBALS['menu'] = $aMenu;
		if($aMenu[$sPath]) {
			$GLOBALS['menu_active_item'] = $aMenu[$sPath];
			$out = Menu::ExecuteMenuItem($aMenu[$sPath]);
		}
		elseif($suggestItem){
			$GLOBALS['menu_active_item'] = $suggestItem;
			$out = Menu::ExecuteMenuItem($suggestItem);
			
		}
		else
			$out = Menu::NotFound();
		return $GLOBALS['sContent'] = $out;
	}
	
	public static function ExecuteMenuItem($aMenuItem){
		$aMenuItem = $aMenuItem + self::DefaultItem();
		if(!User::Access($aMenuItem['rules']))
			$out = Menu::AccessDenied();
		else {
			$GLOBALS['page_title'] = $aMenuItem['title'];
			
			$arguments = $aMenuItem['arguments']?$aMenuItem['arguments']:array();
			if($sFile = $aMenuItem['file'])
				require Module::GetPath($aMenuItem['module']) . DIRECTORY_SEPARATOR . $sFile . '.class.php';
			$out = call_user_func_array($aMenuItem['callback'],$arguments);
		}	
		return $out;
	}
	
	public static function NotFound(){
                header("HTTP/1.0 404 Not Found");
                Theme::SetTitle('Страница не найдена');
		return 'К сожалению, запрашиваемая Вами страница не найдена.';
	}
	
	public static function AccessDenied(){
                header("HTTP/1.0 403 Access Denied");
		Theme::SetTitle('Доступ запрещен');
		return 'Доступ к странице запрещен';
	}
	
	public static function GetActiveItem(){
		global $menu,$menu_active_item;
		if($menu_active_item)
			return $menu_active_item;
		else
			return $menu[Path::Get()];
	}
	
	private static function DefaultItem(){
		return array(
			'type'  	=> 	'STANDART', // STANDART, CALLBACK,
			'title'		=> 	'',
			'file'		=> 	false,
			'menu'		=>	'DEFAULT',
			'rules'		=>	array('Обычный доступ'),
			'callback'	=> 	null,
		);
	}
	
	public static function GetMenu(){
		$aMenu = $GLOBALS['menu'];
		foreach($aMenu as $path=>$aMenuItem)
			$aMenu[$path]	=	$aMenuItem + Menu::DefaultItem();
		return $aMenu;
	}
	
	public static function Render(){
		$aMenu = array();
		foreach(Menu::GetMenu() as $path=>$aMenuItem){
			if($aMenuItem['type'] == 'STANDART') 
				if(User::Access($aMenuItem['rules'])) 
					$aMenu[] = Theme::Render('link',$path,$aMenuItem['title']);
		}
		return Theme::Render('menu',$aMenu);
	}
}