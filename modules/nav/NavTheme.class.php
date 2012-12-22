<?php
class NavTheme {
	public static function NavItem($aItem){
		$oItem = $aItem['#item'];
		if(!$oItem)
			return;
		if($aItem['#child']){
			$submenu = Theme::Render('navigation',$aItem['#child']);
		}
		$item = '<li class="menu-item menu-item-'.$oItem->menu_item_id. ((Path::Get() == $oItem->path)||(Path::GetOrign() == $oItem->path)?' active':'') .'">'.Theme::Render('link',$oItem->path,$oItem->title). $submenu . '</li>';
		return $item;
	}
	
	public static function Navigation($menu){
		$out = '<ul class="menu">';
		foreach($menu as $item)
			$out .= Theme::Render('nav-item',$item);
		return $out . '</ul>';
	}
}