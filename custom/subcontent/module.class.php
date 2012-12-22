<?php
class subcontent {
	public function Init(){
		Event::Bind('ContentLoad','subcontent::EventContentLoad');
		Event::Bind('FormLoad','subcontent::EventFormLoad');
		Event::Bind('ContentDelete','subcontent::EventContentDelete');
	}
	
        public function Rules(){
            return array('Использовать подматериалы');
        }        
        
	public function Menu(){
		return array(
			'ajax/subcontent/autocomplete'=>array(
				'type'=>'callback',
				'callback'=>'subcontent::Autocomplete',
				'rules'	=>	array('Использовать подматериалы'),
			),
			'admin/subcontent/sort'=>array(
				'title'		=>		'Сортировать подматериалы',
				'callback'	=>		'subcontent::SortPage',
				'rules'		=> 		array('Использовать подматериалы'),
			),
			'admin/subcontent/sort_callback'=>array(
				'type'		=>		'callback',
				'callback'	=>		'subcontent::SortCallback',
				'rules'		=>		array('Использовать подматериалы'),
			),
		);
	}	
	
	public static function EventContentLoad(&$data){
		if((Path::Arg(0) == 'content') && (Path::Arg(2) == 'edit'))
			return;
		if($data)
			$data->data = self::GetSubContentMenu($data->id) . $data->data;
	}
	
	public static function EventContentDelete(&$oContent){
		global $pdo;
		if(!$oContent->id)
			return;
		$pdo->query("DELETE FROM subcontent WHERE parent = ? OR content_id = ?",array($oContent->id,$oContent->id));
	}
	
	public static function EventFormLoad(&$aForm){
		if(($aForm['id'] != 'content-add-form') || (!User::Access('Использовать подматериалы')))
			return;
		Theme::AddJs(Module::GetPath('subcontent') . DS . 'js' . DS . 'subcontent.js');
		if(Path::Arg(2) == 'edit'){
			global $pdo;
			$q = $pdo->query("SELECT * FROM content c WHERE id IN (SELECT parent FROM subcontent sc WHERE sc.content_id = ?) ",array(Path::Arg(3)));
			$i = -10 - $q->rowCount() - 2;
			$aForm['content'][$i++] = '<b>Материал помещен в:</b><br>';
			while($res = $pdo->fetch_object($q))
				$aForm['content'][$i++] = '<div class="sc-parent">'.$res->title .' <a href="#" class="sc-parent-remove" style="font-size:11px;">[убрать]</a>'
				. '<input type="hidden" name="parent[]" value="'.$res->id.'" /></div>';
		}
		$aForm['content'][-10] = Theme::Render('autocomplete','parent[]','Поместить материл в:','ajax/subcontent/autocomplete','',array('class'=>'subcontent-parent'));
		$aForm['submit'][] = 'subcontent::ContentFormSubmit';
		
	}
	
	public static function ContentFormSubmit(&$aResult){
		global $pdo;
		if(!$aResult['content_id'])
			return;
		if(!$_POST['parent'])
			return;
		$pdo->query("DELETE FROM subcontent WHERE content_id = ?",array($aResult['content_id']));
		foreach($_POST['parent'] as $p_id)if($p_id)
			$pdo->insert('subcontent',array(
				'parent'=>$p_id,
				'content_id'=>$aResult['content_id'],
			));
		Notice::Message('Подматериал добавлен');
	}
	
	public static function Autocomplete(){
		global $pdo;
		$q = $pdo->query("SELECT * FROM content WHERE title LIKE ?",array($_GET['query'].'%'));
		$match = array();
		while($res = $pdo->fetch_object($q))
			$match[$res->id] = $res->title;
		echo json_encode(array(
			'query'=>$_GET['query'],
			'suggestions'=>array_values($match),
			'data'=>array_keys($match),
		));exit;
	}
	
	public static function GetSubContentMenu($parent){
		global $pdo;
		$items = '';
		$q = $pdo->query("SELECT * FROM subcontent sc LEFT JOIN content c ON sc.content_id = c.id WHERE sc.parent = ? ORDER BY weight",array($parent));
		while($res = $pdo->fetch_object($q)){
			$submenu = self::GetSubContentMenu($res->id);
			$items .= '<li>'.Theme::Render('link','content/'.$res->id,$res->title).$submenu .'</li>';
		}
		if($items)
			return $menu = '<ul class="subcontent-menu">'.$items.'</ul>';
	}
	
	public static function SortPage(){
		jQueryUI::Load();
		Theme::AddJs(Module::GetPath('subcontent') . DS . 'js' . DS . 'sort.js');
		return Form::GetForm('subcontent::SortForm');
	}
	
	public static function SortForm(){
		return array(
			'id'		=>	'subcontent-sort-form',
			'type'		=>	'callback',
			'callback'	=>	'subcontent::SortFormBuilder',
			'submit'	=>	array('subcontent::SortFormSubmit'),
			'ajax'		=>	true,
		);
	}
	
	public static function SortFormBuilder(){
		$form = array();
		$form[] = Theme::Render('autocomplete','parent','Сортировать подматериалы у материала:','ajax/subcontent/autocomplete','',array('id'=>'sc-sort-get'));
		$form[] = '<div><a href="#" class="btn btn-success hide" id="sc-get">Показать</a></div>';
		$form[] = '<div id="subcontent-sort-area"></div>';
		$form[] = Theme::Render('form-actions',array(
			'submit'=>array('text'=>'Сохранить'),
		));
		return $form;
	}
	
	public static function SortCallback(){
		global $pdo;
		$parent = Path::Arg(3);
		if(!$parent)
			return Menu::NotFound();
		$q = $pdo->query("SELECT * FROM subcontent sc LEFT JOIN content c ON sc.content_id = c.id WHERE sc.parent = ? ORDER BY weight",array($parent));
		$out = '';
		$prepend = '<span class="ui-icon ui-icon-arrowthick-2-n-s" style="display: inline-block;margin-bottom: -2px;"></span>';
		while($content = $pdo->fetch_object($q)){
			$out .= '<li>'.$prepend.'<span>'.$content->title.'</span><input type="hidden" name="weight['.$content->id.']" value="'.$content->weight.'"/></li>';
		}
		if($out)
			$out = '<ul id="subcontent-list">'.$out.'</ul>';
		else 
			$out = '<div class="">Подматериалов не найдено.</div>';
		Core::Json($out);
	}
	
	public static function SortFormSubmit(&$aResult){
		global $pdo;
		if((!$_POST['weight'])||(!is_array($_POST['weight'])))
			return Notice::Error('Необходимо выбрать родительский материал');
		foreach($_POST['weight'] as $id => $weight)if($id){
			$pdo->query("UPDATE subcontent SET weight = ? WHERE content_id = ?",array($weight,$id));
		}
		Notice::Message('Порядок подматериалов сохранен');
	}
}