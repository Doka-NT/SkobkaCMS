<?php
class Nav {
	public static function Menu(){
		return array(
			'admin/menu'=>array(
				'title'	=>	'Управление меню',
				'callback'	=>	'NavAdmin::MainPage',
				'file'		=>	'NavAdmin',
				'rules'		=>	array('Управление меню'),
			),
			'admin/menu/delete'=>array(
				'type'		=>	'callback',
				'file'		=>	'NavAdmin',
				'callback'	=>	'NavAdmin::DeleteMenu',
				'rules'		=>	array('Управление меню'),
			),
			'admin/menu/menu'=>array(
				'type'		=>	'callback',
				'callback'	=>	'NavAdmin::MenuPage',
				'file'		=>	'NavAdmin',
				'rules'		=>	array('Управление меню'),
			),		
			'admin/menu/menu/delete'=>array(
				'type'		=>	'callback',
				'callback'	=>	'NavAdmin::DeleteMenuItem',
				'file'		=> 	'NavAdmin',
				'rules'		=>	array('Управление меню'),
			),
			'admin/menu/menu/save_weight'=>array(
				'type'		=>	'callback',
				'callback'	=>	'NavAdmin::SaveWeight',
				'file'		=>	'NavAdmin',
				'rules'		=>	array('Управление меню'),
			),
		);
	}
	
        public function Rules(){
            return array('Управление меню');
        }
        
	public static function GetItems($parent = 0, $menu_id = 0){
		global $pdo;
		$args[] = $parent;
		if($menu_id){
			$sql = ' AND menu_id = ?';
			$args[] = $menu_id;
		}
		$items = array();
		$q = $pdo->query("SELECT * FROM menu_items WHERE parent = ? {$sql} ORDER BY weight",$args);
		while($menu_item = $pdo->fetch_object($q)){
			$items[$menu_item->menu_item_id]['#item'] = $menu_item;
			$items[$menu_item->menu_item_id]['#child'] = Nav::GetItems($menu_item->menu_item_id);
		}
		return $items;
	}
	
	public static function Get($menu_id){
		$menu = Nav::GetItems(0,$menu_id);
		return $menu;
	}
	
	public static function BlockInfo(){
		global $pdo;
		$blocks = array();
		$q = $pdo->query("SELECT * FROM menu");
		while($menu = $pdo->fetch_object($q)){
			$blocks['block-menu-'.$menu->menu_id] = array(
				'title'		=>	$menu->title,
				'content'	=>	Nav::Render($menu->menu_id),
			);
		}
		return $blocks;
	}
	
	public static function Render($menu_id){
		return Theme::Render('navigation',Nav::Get($menu_id));
	}
	
	public function Theme(){
		return array(
			'nav-item'=>array(
				'type'	=>	'callback',
				'callback'	=>	'NavTheme::NavItem',
				'file'		=>	Module::GetPath('Nav') . DS . 'NavTheme.class.php',
			),
			'navigation'	=>	array(
				'type'		=>	'callback',
				'callback'	=>	'NavTheme::Navigation',
				'file'		=>	Module::GetPath('Nav') . DS . 'NavTheme.class.php',
			),
		);
	}
}