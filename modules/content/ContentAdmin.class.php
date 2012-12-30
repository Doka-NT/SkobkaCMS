<?php

class ContentAdmin {
	public static function Main () {
		global $pdo;
		$q = $pdo->query("SELECT * FROM content_type");
		while($type = $pdo->fetch_object($q)){
			$rows[] = array(
				$type->name,
				$type->description,
				Theme::Render('link','admin/content/add/'.$type->type,'Создать'),
				Theme::Render('link-confirm','admin/content/delete/'.$type->type_id,'Удалить'),
			);
		}
		if(!$rows)
			$rows[] = array('Типов материла нет');
		
		return Theme::Render('table',$rows,array(
			'Тип','Описание','Действия','',
		)) . Form::GetForm('ContentAdmin::TypeAddForm');
	}
	
	
	public static function TypeAddForm(){
		Theme::AddCss(Module::GetPath('Content') . DS . 'css' . DS . 'type-add-form.css');
		
		return array(
			'id'		=>	'type_add_form',
			'type'		=>	'template',
			'required'	=>	array('Название типа'=>'name','Идентификатор типа'=>'type'),
			'template'	=>	Module::GetPath('Content') . DS . 'theme' . DS . 'type-add-form.tpl.php',
			'validate'	=>	array('ContentAdmin::TypeAddFormValidate'),
			'submit'	=>	array('ContentAdmin::TypeAddFormSubmit'),
		);
	}
	
	public static function TypeAddFormValidate(&$aResult){
		if(!preg_match('/([A-Za-z0-9_]+)/',$_POST['type'])) {
			Notice::Error('Индектификатор типа может содержать латинские символы и знак подчеркивания');
			$aResult['validate_success'] = FALSE;
		}
	}
	
	public static function TypeAddFormSubmit(&$aResult){
		global $pdo;
		$res = $pdo->insert('content_type',$data = array(
			'name'=>$_POST['name'],
			'type'=>$_POST['type'],
			'description'=>$_POST['description'],
		));
				
		Notice::Message('Новый тип материала добавлен');
	}
	
	public static function ContentTypeDelete(){
		global $pdo;
		$args = Path::Explode();
		$type_id = $args[3];
		$pdo->query("DELETE FROM content_type WHERE type_id = ?",array($args[3]));
		Notice::Message('Тип материала удален');
		Path::Back();
	}
	
	public static function ContentAdd(){
		//Editor::Load();
                return Form::GetForm('ContentAdmin::ContentAddForm');
	}
	
	public static function ContentAddForm(){
		return array(
			'id'		=>	'content-add-form',
			'type'		=>	'template',
			'template'	=>	Module::GetPath('Content') . DS . 'theme' . DS . 'content-add-form.tpl.php',
			'validate'	=>	array(),
			'arguments'	=>	array('oContent'=>NULL),
			'required'	=>	array('content_name','content_content'),
			'submit'	=>	array('ContentAdmin::ContentAddFormSubmit'),
		);
	}
	
	public static function ContentAddFormSubmit(&$aResult){
		global $pdo,$user;
		$content_id = (int)Path::Arg(3);
                $added = true;
		if(!$content_id){
			$pdo->insert('content',array(
				'title'=>$_POST['content_name'],
				'data'=>$_POST['content_content'],
				'created'=>time(),
				'status'=>$_POST['content_status'],
				'type'=>$_POST['content_content_type'],
                                'uid'=>$user->uid,
			));
			Notice::Message('Материал добавлен');
		}else{
			$pdo->query("UPDATE content SET title = ?, data = ?, status = ? WHERE id = ?",array(
				$_POST['content_name'],
				$_POST['content_content'],
				$_POST['content_status'],
				$content_id,
			));
			Notice::Message('Материал обновлен');
                        $added = false;
		}
		$content_id = $content_id?$content_id:$pdo->lastInsertId();
		$aResult['content_id'] = $content_id;
		$aResult['replace'] = Path::Url('content/'.$content_id);
                $oContent = Content::Load($content_id);
                if($added)
                    Event::Call('ContentAdd',$oContent);
                else 
                    Event::Call ('ContentEdit',$oContent);
	}
	
	public static function ContentEdit(){
		$oContent = Content::Load(Path::Arg(3));
		//Editor::Load();
		return Form::GetForm('ContentAdmin::ContentAddForm',$oContent);
	}
	
	public static function ContentDelete(){
		global $pdo;
		$id = Path::Arg(3);
		$content = Content::Load($id);
		Path::DeleteAlias('content/'.$id);
		Event::Call('ContentDelete',$content);
		$pdo->query("DELETE FROM content WHERE id = ?",array($id));
		Notice::Message('Материал удален');
		Path::Back();
	}
}