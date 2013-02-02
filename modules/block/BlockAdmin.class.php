<?php

class BlockAdmin {

    public static function MainPage() {
        Theme::AddJs(Module::GetPath('block') . DS . 'theme' . DS . 'block-list.js');
        return Form::GetForm('BlockAdmin::ListForm', Block::GetList());
    }

    public static function ListForm($aBlocks = array()) {
        return array(
            'id' => 'block-list-form',
            'type' => 'template',
            'template' => Module::GetPath('Block') . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . 'block-list-form.tpl.php',
            'arguments' => array('blocks' => array()),
            'submit' => array('BlockAdmin::ListFormSubmit'),
        );
    }

    public static function ListFormSubmit(&$aResult) {
        global $pdo;
        $pdo->query("TRUNCATE blocks");
        if ($_POST['block'])
            foreach ($_POST['block'] as $block_id => $aBlock) {
                $pdo->insert('blocks', array(
                    'block_id' => $block_id,
                    'position' => $aBlock['position'],
                    'weight' => (int) $aBlock['order'],
                    'pages' => $aBlock['pages'],
                    'not_pages' => $aBlock['not_pages'],
                    'show_title'=>$aBlock['show_title'],
                ));
            }
        Notice::Message('Настройки блоков сохранены');
    }

    public static function AddBlock() {
        return Form::GetForm('BlockAdmin::AddBlockForm');
    }

    public static function AddBlockForm() {
        return array(
            'id' => 'add-block-form',
            'type' => 'template',
            'template' => Module::GetPath('block') . DS . 'theme' . DS . 'add-block-form.tpl.php',
            'required' => array('title', 'content'),
            'submit' => array('BlockAdmin::AddBlockFormSubmit'),
        );
    }

    public static function AddBlockFormSubmit(&$aResult) {
        global $pdo;
        $pdo->insert('blocks_custom', array(
            'title' => $_POST['title'],
            'content' => $_POST['content'],
        ));
        Notice::Message('Блок добавлен');
        Path::Replace('admin/block');
    }

    public static function EditBlock() {
        global $pdo;
        $id = (int) str_replace('block-custom-', '', Path::Arg(3));

        $block = $pdo->fetch_object($pdo->query("SELECT * FROM blocks_custom WHERE bcid = ?", array($id)));
        $block->content = $block->content;


        return Form::GetForm("BlockAdmin::EditBlockForm", $block);
    }

    public static function EditBlockForm() {
        return array(
            'id' => 'edit-block-form',
            'type' => 'template',
            'template' => Module::GetPath('Block') . DS . 'theme' . DS . 'edit-block-form.tpl.php',
            'required' => array('title', 'content', 'block_id'),
            'arguments' => array('block' => NULL),
            'submit' => array('BlockAdmin::EditBlockFormSubmit'),
        );
    }

    public static function EditBlockFormSubmit(&$aResult) {
        global $pdo;
        $pdo->query("UPDATE blocks_custom SET title = ? , content = ? WHERE bcid = ?", array(
            $_POST['title'],
            $_POST['content'],
            $_POST['block_id'],
        ));
        Notice::Message('Блок обновлен');
        Path::Back();
    }
    
    public static function DeleteCustomBlock(){
        $block_id = Path::Arg(3);
        preg_match_all('/block-custom-(\d+)/',$block_id,$m);
        $custom_id = (int)$m[1][0];
        global $pdo;
        $pdo->query("DELETE FROM blocks WHERE block_id = ?",array($block_id));
        $pdo->query("DELETE FROM blocks_custom WHERE bcid = ?",array($custom_id));
        Notice::Message('Блок удален');
        Path::Back();
    }
}