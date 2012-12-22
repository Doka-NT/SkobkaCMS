<?php

class ContentPages {
	public static function Page(){
		$args = Path::Explode();
		if($args[1])
			return self::PageView($args[1]);
                else
                    return Menu::NotFound ();
	}
	
	public static function PageView($id){
		$oData = Content::Load($id);
		if(!$oData)
			return Menu::NotFound();
		Theme::SetTitle($oData->title);
		$control_links = array();
		if(User::Access('Управление материалами'))
			$control_links[] = Theme::Render('link','content/content/edit/'.$oData->id,'Редактировать');
		//return ($control_links?'<div class="control-links">'.implode(' || ',$control_links).'</div>':'') . Theme::Render('ContentPage',$oData);
                $args = array('content'=>&$oData,'control-link'=>&$control_links);
                Event::Call('ContentView', $args);
                if($control_links)
                    $oData->data = '<div class="control-links">'.implode(' || ',$control_links).'</div>' . $oData->data;
		return Theme::Template(Module::GetPath('content') . DS . 'theme' . DS . 'content.tpl.php',array('content'=>$oData),$oData->type);
	}
        
        public static function PageList(){
		global $pdo;
		$q = $pdo->query("SELECT c.*,ct.name as type FROM content c LEFT JOIN content_type ct USING(type) ORDER BY c.created DESC");
		$rows = array();
		$thead = array(
			'ID','Заголовок','Тип','Дата создания','Статус','Действия','',
		);
		while($data = $pdo->fetch_object($q)){
			$rows[] = array(
				$data->id,
				Theme::Render('link','content/'.$data->id,$data->title),
				$data->type?$data->type:'<span style="color:red;">Ошибка! Тип не существует</span>',
				Theme::Render('date',$data->created),
				$data->status?'Опубликовано':'Не опубликовано',
				Theme::Render('link','content/content/edit/'.$data->id,'Редактировать'),
				Theme::Render('link-confirm','content/content/delete/'.$data->id,'Удалить'),
			);
		}
		return Theme::Render('table',$rows,$thead);            
        }
}