<?php

class jQueryUI {
	private static function Required(){
		Theme::AddJs(Module::GetPath('jqueryui') . DS . 'js' . DS . 'jquery-ui-1.9.1.custom.min.js');
		Theme::AddCss(Module::GetPath('jqueryui') . DS . 'css' . DS . 'ui-lightness' . DS . 'jquery-ui-1.9.1.custom.min.css');
	}

	public static function Load(){
		self::Required();
	}
}