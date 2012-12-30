<?php

class Content {

    public function Init() {
        Module::IncludeFile('content', 'ContentEvent.class.php');
        Event::Bind('SitemapRebuild', 'ContentEvent::SitemapRebuild');
    }

    public function Rules() {
        $rules = array('Обычный доступ', 'Управление материалами');
        foreach (Content::GetTypes() as $type => $name) {
            $rules[] = 'Создавать ' . $type;
        }
        return $rules;
    }

    public function Menu() {
        return array(
            'content' => array(
                //'title'		=>	'Список',
                'type' => 'callback',
                'callback' => 'ContentPages::Page',
                'file' => 'ContentPages',
                'rules' => array('Обычный доступ'),
            ),
            'admin/content/list' => array(
                'title' => 'Список материалов',
                'callback' => 'ContentPages::PageList',
                'file' => 'ContentPages',
                'rules' => array('Управление материалами'),
                'group'=>'Содержимое'
            ),
            'admin/content' => array(
                'title' => 'Управление материалами',
                'callback' => 'ContentAdmin::Main',
                'file' => 'ContentAdmin',
                'rules' => array('Управление материалами'),
                'group'=>'Содержимое'
            ),
            'admin/content/delete' => array(
                'type' => 'callback',
                'callback' => 'ContentAdmin::ContentTypeDelete',
                'file' => 'ContentAdmin',
                'rules' => array('Управление материалами'),
            ),
            'admin/content/add' => array(
                'type' => 'callback',
                'callback' => 'ContentAdmin::ContentAdd',
                'file' => 'ContentAdmin',
                'rules' => array('Создавать ' . Path::Arg(3), 'Управление материалами'),
                'title' => 'Создание материала',
            ),
            'content/content/edit/' => array(
                'type' => 'callback',
                'callback' => 'ContentAdmin::ContentEdit',
                'file' => 'ContentAdmin',
                'rules' => array('Управление материалами'),
            ),
            'content/content/delete/' => array(
                'type' => 'callback',
                'callback' => 'ContentAdmin::ContentDelete',
                'file' => 'ContentAdmin',
                'rules' => array('Управление материалами'),
            ),
        );
    }

    public static function Load($id) {
        global $pdo;
        $q = $pdo->query("SELECT * FROM content WHERE id = ?", array($id));
        $data = $pdo->fetch_object($q);
        Event::Call('ContentLoad', $data);
        return $data;
    }

    public static function TypeLoad($arg) {
        global $pdo;

        if (is_int($arg))
            $q = $pdo->query("SELECT * FROM content_type WHERE type_id = ?", array($arg));
        else
            $q = $pdo->query("SELECT * FROM content_type WHERE type LIKE ?", array($arg));

        $oType = $pdo->fetch_object($q);
        $arg = array('type' => &$oType);
        Event::Call('TypeLoad', $arg);
        return $oType;
    }

    public function Theme() {
        return array(
            'ContentPage' => array(
                'type' => 'template',
                'template' => Module::GetPath('Content') . DS . 'theme' . DS . 'content.tpl.php',
                'arguments' => array('content' => NULL),
            ),
        );
    }

    public static function GetTypes() {
        global $pdo;
        $q = $pdo->query("SELECT * FROM content_type");
        $types = array();
        while ($type = $pdo->fetch_object($q))
            $types[$type->type] = $type->name;
        return $types;
    }

}