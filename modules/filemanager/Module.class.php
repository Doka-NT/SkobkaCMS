<?php

class Filemanager {

    public function Menu() {
        return array(
            'admin/files' => array(
                'title' => 'Файлменеджер',
                'rules' => array('Использовать файлы'),
                'callback' => 'Filemanager::Files',
                'group'=>'Содержимое'
            ),
            'ajax/admin/files' => array(
                'type' => 'callback',
                'rules' => array('Использовать файлы'),
                'callback' => 'Filemanager::Callback',
            ),
        );
    }

    public function Rules() {
        return array('Использовать файлы');
    }

    public static function Load() {
        return self::ElFinderLoad();
    }

    public static function Files() {
        if ($_GET['tinymce']) {
            $direct = true;
            $file = Theme::Template(Module::GetPath('filemanager') . '/elfinder-page.php', array('direct' => true));
            exit($file);
        }
        return '<iframe src="/' . Module::GetPath('filemanager') . '/elfinder-page.php?skey=' . FM_SECURITY_KEY . '&tinymce=' . $_GET['tinymce'] . '" width="100%" height="600"></iframe>';
    }

    public static function Callback() {
        return self::CallbackElFinder();
    }

    public static function ElFinderLoad() {
        Theme::AddJs('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',false);
        Theme::AddJs(Module::GetPath('filemanager') . DS . 'js' . DS . 'elfinder.min.js',false);
        Theme::AddJs(Module::GetPath('filemanager') . DS . 'js' . DS . 'i18n' . DS . 'elfinder.ru.js',false);

        Theme::AddCss('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
        Theme::AddCss(Module::GetPath('filemanager') . DS . 'css' . DS . 'elfinder.min.css');
        Theme::AddCss(Module::GetPath('filemanager') . DS . 'css' . DS . 'theme.css');

        Theme::AddJs(Module::GetPath('filemanager') . DS . 'init.js');
    }

    public static function CallbackElFinder() {
        include Module::GetPath('filemanager') . DS . 'php' . DS . 'connector.php';
    }

}