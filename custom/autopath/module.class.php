<?php

class Autopath {
	public static function Init(){
		Event::Bind('FormLoad','Autopath::EventFormLoad');
	}
	
        public function Rules(){
            return array('Использовать АвтоЧПУ');
        }
        
	public static function AutoAlias($str,$prefix = false){
		$tr = array(
			"А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
			"Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
			"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
			"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
			"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
			"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
			"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
			" "=> "-", "."=> "", "/"=> "_"
		);
		$str = strtr($str,$tr);
		$path = preg_replace('/[^A-Za-z0-9_\-]/', '', $str);
                if(!$prefix)
                    return $path;
                return $prefix . '/' . $path;
                   
	}
	
	private static function GetFormsId(){
		return array('content-add-form');
	}
	
	public static function EventFormLoad(&$aForm){
		if(!in_array($aForm['id'],self::GetFormsId()))
			return;
		Theme::AddJs(Module::GetPath('autopath') . DS . 'js' . DS . 'autopath.js');
		$path = Path::Arg(2) == 'edit'?Path::GetAlias('content/'.Path::Arg(3)):'';
		$aForm['content'][-5] = Theme::Render('input','text','alias','URL путь (ЧПУ)',$path,$path?array():array('disabled'=>true));
		$chbx_attr = array('class'=>'autoalias-checkbox');
		if(!$path)
			$chbx_attr['checked'] = 'true';
		$aForm['content'][-4] = Theme::Render('input','checkbox','autoalias','Создать автоматически',true,$chbx_attr);
		$aForm['submit'][] = 'Autopath::SaveAutoPath';
	}
	
	public static function SaveAutoPath(&$aResult){
		global $pdo;
		$id = $aResult['content_id'];
                $oContent = Content::Load($id);
		if(!$id)
			return;
		$alias = $_POST['autoalias'] == 'on'?self::AutoAlias($_POST['content_name'],$oContent->type):$_POST['alias'];
		if(!$alias)
			return;
		$pdo->query("DELETE FROM url_alias WHERE path LIKE ?",array('content/'.$id));
		
		//Check for existing alias
		$alias = Path::PrepareAlias($alias);
		
		$pdo->insert('url_alias',array(
			'path'=>'content/'.$id,
			'alias'=>$alias,
		));
		$aResult['replace'] = Path::Url($alias);
		Notice::Message('Псевдоним добавлен');			
		
	}
}