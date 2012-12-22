<?php

class AdminTheme {
	public static function Dump($arg1){
		return '<pre>'.print_r($arg1,1).'</pre>';
	}
	
	public static function Link($path,$title,$attributes = array()){
		return '<a href="' . Path::Url($path) . '"' . Theme::Attr($attributes) . '>'.$title.'</a>';
	}
	
	public static function LinkConfirm($path,$title,$attributes = array()){
		$attributes['class'] .= ' link-confirm';
		return '<a href="' . Path::Url($path) . '"' . Theme::Attr($attributes) . '>'.$title.'</a>';
	}	

	public static function LinkAjax($path,$title,$attributes = array()){
		$attributes['class'] .= ' link-ajax';
		return '<a href="' . Path::Url($path) . '"' . Theme::Attr($attributes) . '>'.$title.'</a>';
	}	
	
	public static function Menu($aMenu){
		$out = '';
		foreach($aMenu as $k=>$link)
			$out .= '<li class="menu-item menu-item-'.$k.'">'.$link.'</li>';
		return '<ul class="menu">'.$out.'</ul>';
	}
	
	public static function Table($aRows,$aThead = array()){
		$aRows = $aRows?$aRows:array(array('Нет данных'));
		$thead = '';
		foreach($aThead as $th){
			$thead .= '<th>'.$th.'</th>';
		}
		$rows = '';
		foreach($aRows as $row)
			if(is_array($row)){
				$cell = '';
				if($row['#attributes']){
					$attr = ' '.Theme::Attr($row['#attributes']).' ';
					unset($row['#attributes']);
				}
				foreach($row as $td)
					$cell .= '<td>'.$td.'</td>';
				$rows .= '<tr'.$attr.'>'.$cell.'</tr>';
			}
			elseif(is_string($row)) {
				$rows .= $row;
			}
		return '<table class="table table-condensed">' . ($thead?'<thead>' . $thead . '</thead>':'') . '<tbody>' . $rows . '</tbody></table>';
	}
	
	public static function Date($timestamp,$format = ''){
		if(!$format)
			$format = 'd.m.Y - H:i';
		return date($format,$timestamp);
	}
	
	public static function Form($aForm){
		//		$form_id_token = md5(
		if(is_array($aForm['content']))
			$aForm['content'] = implode("",$aForm['content']);
		return '<form id="'.$aForm['id'].'" action="'.$aForm['action'].'" method="post">'.$aForm['content'].'</form>';
	}
}