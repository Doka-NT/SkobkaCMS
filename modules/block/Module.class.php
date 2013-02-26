<?php

class Block {
    public $EventUpdate = 'Block::EventUpdate';
    public static function GetByPosition($position, &$aBlocks,$theme = false) {
        global $pdo;
	if(!$theme)
	    $theme = $GLOBALS['theme'];
        $out = '';
        //$q = $pdo->query("SELECT * FROM blocks b WHERE b.position LIKE ? AND (b.theme LIKE ? OR b.theme LIKE '*')  ORDER BY b.weight", array($position,$theme));
	$q = $pdo->query("SELECT * FROM blocks b WHERE b.position LIKE ? ORDER BY b.weight", array($position));
        while ($block = $pdo->fetch_object($q)) {
            if (($aBlocks[$block->block_id]) && Block::Visible($block)) {
                $_block = $aBlocks[$block->block_id];
                $block = $_block + (array) $block;
                $block = (object) $block;
                if ($block->content)
                    $out .= Theme::Render('block', $block);
            }
        }
        return $out;
    }

    public function __construct() {
        
    }
    
    public function Rules(){
        return array('Управление блоками');
    }
    
    public function Menu() {
        return array(
            'admin/block' => array(
                'title' => 'Список',
                'callback' => 'BlockAdmin::MainPage',
                'file' => 'BlockAdmin',
                'rules' => array('Управление блоками'),
                'group'=>'Блоки'
            ),
            'admin/block/add' => array(
                'title' => 'Добавить блок',
                //'type'		=>	'callback',
                'callback' => 'BlockAdmin::AddBlock',
                'file' => 'BlockAdmin',
                'rules' => array('Управление блоками'),
                'group'=>'Блоки'
            ),
            'admin/block/block' => array(
                'type' => 'callback',
                'callback' => 'BlockAdmin::EditBlock',
                'file' => 'BlockAdmin',
                'rules' => array('Управление блоками'),
            ),
            'admin/block/delete'=>array(
                'type'=>'callback',
                'callback'=>'BlockAdmin::DeleteCustomBlock',
                'file'=>'BlockAdmin',
                'rules'=>array('Управление блоками'),
            ),
        );
    }

    public static function GetList() {
        global $oEngine;
        $blocks = array();
        foreach ($oEngine->modules as $oModule)
            if (method_exists($oModule, 'BlockInfo'))
                $blocks += $oModule->BlockInfo();
        return $blocks;
    }

    public static function BlockInfo() {
        /* return array(
          'test-block-1'=>array(
          'title' 	=> 	'Тестовый блок',
          'content'	=> 	Block::Block1(),
          'path'		=>	'',
          //'collapse'	=>	TRUE,
          'collapsed'	=>	FALSE,
          ),
          ); */
        global $pdo;
        $q = $pdo->query("SELECT * FROM blocks_custom");
        $aBlocks = array();
        while ($block = $pdo->fetch_object($q))
            $aBlocks['block-custom-' . $block->bcid] = array(
                'title' => $block->title,
                'content' => $block->content,
            );
        return $aBlocks;
    }

    public static function Block1() {
        return 'some test content';
    }

    public static function Visible($block) {
        if ($block->pages || $block->not_pages) {
            $pages = explode("\n", $block->pages);
            $not_pages = explode("\n", $block->not_pages);
            $show = false;
            if ($pages) {
                foreach ($pages as $path)
                    if ($path) {
                        $path = trim($path);
                        if ($path == 'frontpage') {
                            if (Path::IsFront())
                                $show = true;
                        }
                        else {
                            if ((Path::PathMatch(Path::Get(), $path)) || (Path::PathMatch(Path::GetOrign(), $path)))
                                $show = true;
                        }
                        $_show = true;
                    }
            }

            if (!$_show)
                $show = true;
            if ($not_pages) {
                foreach ($not_pages as $path)
                    if ($path) {
                        $path = trim($path);
                        if ($path == 'frontpage') {
                            if (Path::IsFront())
                                $show = false;
                        }
                        else {
                            if ((Path::PathMatch(Path::Get(), $path)) || (Path::PathMatch(Path::GetOrign(), $path)))
                                $show = false;
                        }
                    }
                //$_hide = true;
            }
            return $show;
        }
        return true;
    }
    
    public static function EventUpdate($version){
	global $pdo;
	$updated_to = Update::GetModuleUpdatedVersion('block');
	if($updated_to < $version){
	    if($version == 300){
		$pdo->query("ALTER TABLE  `blocks` ADD  `theme` VARCHAR( 255 ) NOT NULL",array());
		Update::Save('block',$version);
	    }
	}
    }
}