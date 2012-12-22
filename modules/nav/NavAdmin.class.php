<?php

class NavAdmin {
	public static function MainPage(){
		global $pdo;
		$rows = array();
		$q = $pdo->query("SELECT * FROM menu m");
		while($menu = $pdo->fetch_object($q))
			$rows[] = array(
				$menu->menu_id,
				Theme::Render('link','admin/menu/menu/'.$menu->menu_id,$menu->title),
				Theme::Render('link-confirm','admin/menu/delete/'.$menu->menu_id,'Удалить'),
			);
		$menuTable = Theme::Render('table',$rows,array(),array('class'=>'table-striped'));
		
		return $menuTable . Form::GetForm('NavAdmin::AddMenu') /*. Form::GetForm('NavAdmin::MenuConfig')*/;
	}
	
	public static function AddMenu(){
		return array(
			'id'	=>	'menu-add-form',
			'type'	=>	'template',
			'template'	=> Module::GetPath('Nav') . DS . 'theme' . DS . 'menu-add-form.tpl.php',
			'required'	=>	array('title'),
			'submit'	=>	array('NavAdmin::AddMenuSubmit'),
		);
	}
	
	public static function AddMenuSubmit(&$aResult){
		global $pdo;
		$title = $_POST['title'];
		$pdo->insert('menu',array(
			'title'=>$title,
		));
		Notice::Message('Меню "<b>'.$title.'</b>" создано.');
	}
	
	public static function MenuPage(){
		global $pdo;
		$menu_id = Path::Arg(3);
		$q = $pdo->query("SELECT * FROM menu_items mi WHERE mi.menu_id = ? AND mi.parent = 0 ORDER BY mi.weight",array($menu_id));
		$rows = array();
		while($menu_item = $pdo->fetch_object($q)){
			$rows[] = array(
				$menu_item->menu_item_id,
				Theme::Render('link',$menu_item->path,$menu_item->title),
				$menu_item->path,
				Theme::Render('link-confirm','admin/menu/menu/delete/'.$menu_item->menu_item_id,'Удалить'),
				Theme::Render('input','number','weight['.$menu_item->menu_item_id.']','',(int)$menu_item->weight,array('style'=>'width:25px;text-align:center;','class'=>'weight')),
			);
			$rows[] = NavAdmin::MenuPageGetChild($menu_item->menu_item_id);
		}
		if(!$rows)
			$rows[] = array('Пунктов меню нет');
		Theme::AddJs(Module::GetPath('nav') . DS . 'js' . DS . 'menu_item_weight.js');
		return Theme::Render('table',$rows,array(
			'id','Пункт','Путь','Действия','Вес'
		)) . '<div><a id="save_weight" href="/admin/menu/menu/save_weight" class="btn btn-success">Сохранить</a></div>' . Form::GetForm('NavAdmin::AddMenuItem',$menu_id);
	}
	public static function MenuPageGetChild($parent_id,$level = 1){
		$data = '';
		$i = 0;
		foreach(Nav::GetItems($parent_id) as $aItem){
			if($i == 0)
				$td_style = 'style="padding-left:'. (20 * $level) .'px;"';
			else 
				$td_style = '';
			
			$data .= '<tr>';
			
			$data .= '<td>'.$aItem['#item']->menu_item_id.'</td>';
			$data .= '<td '.$td_style.'>'.Theme::Render('link',$aItem['#item']->path,$aItem['#item']->title).'</td>';
			$data .= '<td>'.$aItem['#item']->path.'</td>';
			$data .= '<td>'.Theme::Render('link','admin/menu/menu/delete/'.$aItem['#item']->menu_item_id,'Удалить');
			
			$data .= '</tr>';
			$data .= NavAdmin::MenuPageGetChild($aItem['#item']->menu_item_id,$level + 1);
		}
		return /*'<tr class="warning"><td colspan="3">Подпункты</td></tr>'.*/$data;
	}
	public static function AddMenuItem(){
		return array(
			'id'		=>	'add-menu-item',
			'type'		=>	'template',
			'template'	=>	Module::GetPath('Nav') . DS . 'theme' . DS . 'add-menu-item.tpl.php',
			'required'	=>	array('title','path'),
			'arguments'	=>	array('menu_id'=>NULL),
			'submit'	=>	array('NavAdmin::AddMenuItemSubmit'),
		);
	}
	
	public static function AddMenuItemSubmit(&$aResult){
		global $pdo;
		$pdo->insert('menu_items',array(
			'title'	=>	$_POST['title'],
			'path'	=>	$_POST['path'],
			'parent'=>	(int)$_POST['parent'],
			'menu_id'=>	$_POST['menu_id'],
		));
		Notice::Message('Пункт меню добавлен');
	}
	
	public static function DeleteMenuItem(){
		global $pdo;
		$item_id = Path::Arg(4);
		$pdo->query("DELETE FROM menu_items WHERE menu_item_id = ?",array($item_id));
		Notice::Message('Пункт меню удален');
		NavAdmin::DeleteChildItem($item_id);
		Path::Back();
	}
	
	public static function DeleteChildItem($parent){
		global $pdo;
		$q = $pdo->query("SELECT * FROM menu_items WHERE parent = ?",array($parent));
		while($menu_item = $pdo->fetch_object($q)){
			Notice::Message('Подпункт <b>'.$menu_item->title.'</b> удален.');
			NavAdmin::DeleteChildItem($menu_item->menu_item_id);
			$pdo->query("DELETE FROM menu_items WHERE menu_item_id = ?",array($menu_item->menu_item_id));
		}
		return true;
	}
	
	public static function MenuConfig(){
		return array(
			'id'		=>	'menu-config-form',
			'type'		=>	'template',
			'template'	=>	Module::GetPath('Nav') . DS . 'theme' . DS . 'menu-config-form.tpl.php',
			'arguments'	=>	array('menu_id'),
			'submit'	=>	array('NavAdmin::MenuConfigSubmit'),
		);
	}
	
	public static function MenuConfigSubmit(&$aResult){
		
	}
	
	public static function DeleteMenu(){
		global $pdo;
		$menu_id = Path::Arg(3);
		$pdo->query("DELETE FROM menu_items WHERE menu_id = ?",array($menu_id));
		$pdo->query("DELETE FROM menu WHERE menu_id = ?",array($menu_id));
		Notice::Message('Меню и все его пункты удалены');
		Path::Back();
	}
	
	public static function SaveWeight(){
		global $pdo;
		$data = $_POST['weight'];
		$weight_list = (array)json_decode($data);
		foreach($weight_list as $menu_item_id=>$weight){
			$pdo->query("UPDATE menu_items SET weight = ? WHERE menu_item_id = ?",array($weight,$menu_item_id));
		}
		exit(json_encode(array('message'=>'Порядок пунктов сохранен')));
	}
}	