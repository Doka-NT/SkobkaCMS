<?php

class ThemeInfo {
	public function Info(){
		return array(
			'css' => array(
				//'css/reset.css',
				'css/style.css',
				//'bootstrap/css/bootstrap.min.css',
				'css/skin.css',
			),
			'js'  => array(
				//'js/jquery-1.8.2.min.js',
				//'js/main.js',
				//'bootstrap/js/bootstrap.min.js',
			),
			
			'positions'=>array(
				'top',
				'top1',
				'maintop',
				'top_columns',
				'footer',
				'user1','user2','user3',
				'absolute',
			),
		);
	}
}