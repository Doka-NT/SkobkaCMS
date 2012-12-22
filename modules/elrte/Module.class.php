<?php
class Elrte {
	public function _construct(){
	
	}
	
	public static function Load(){
		Theme::AddCss(Module::GetPath('elrte') . DS . 'css/smoothness/jquery-ui-1.9.1.custom.min.css');
		Theme::AddCss(Module::GetPath('elrte') . DS . 'css/elrte.min.css');
		
		Theme::AddJs(Module::GetPath('elrte') . DS . 'js/jquery-ui-1.9.1.custom.min.js');
		Theme::AddJs(Module::GetPath('elrte') . DS . 'js/elrte.min.js');
		Theme::AddJs(Module::GetPath('elrte') . DS . 'js/i18n/elrte.ru.js');
		Theme::AddJs(Module::GetPath('elrte') . DS . 'enable.js');
	}
}