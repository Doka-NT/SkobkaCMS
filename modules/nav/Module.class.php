<?php

class Nav {
    /**
     * Инициализация модуля
     */
    public function Init(){
        Module::IncludeFile('Nav', 'NavEvent.class.php');
        Event::Bind('FormLoad', 'NavEvent::EventFormLoad');
    }
    /**
     * Системно-вызываемый метод для управления меню (роутингом)
     * @return array()
     */
    public static function Menu() {
        return array(
            'admin/menu' => array(
                'title' => 'Управление меню',
                'callback' => 'NavAdmin::MainPage',
                'file' => 'NavAdmin',
                'rules' => array('Управление меню'),
                'group'=>'Структура'
            ),
            'admin/menu/delete' => array(
                'type' => 'callback',
                'file' => 'NavAdmin',
                'callback' => 'NavAdmin::DeleteMenu',
                'rules' => array('Управление меню'),
            ),
            'admin/menu/menu' => array(
                'type' => 'callback',
                'callback' => 'NavAdmin::MenuPage',
                'file' => 'NavAdmin',
                'rules' => array('Управление меню'),
            ),
            'admin/menu/menu/delete' => array(
                'type' => 'callback',
                'callback' => 'NavAdmin::DeleteMenuItem',
                'file' => 'NavAdmin',
                'rules' => array('Управление меню'),
            ),
            'admin/menu/menu/save_weight' => array(
                'type' => 'callback',
                'callback' => 'NavAdmin::SaveWeight',
                'file' => 'NavAdmin',
                'rules' => array('Управление меню'),
            ),
            
            'admin/menu/edit'=>array(
                'type'=>'callback',
                'callback'=>'NavAdmin::MenuEdit',
                'file'=>'NavAdmin',
                'rules'=>array('Управление меню'),
            ),
            
            'admin/menu/menu/edit'=>array(
                'type'=>'callback',
                'callback'=>'NavAdmin::MenuItemEdit',
                'file'=>'NavAdmin',
                'rules'=>array('Управление меню'),
            ),
        );
    }
    /**
     * Системно-вызываемый метод для указания возможных правил
     * @return array()
     */
    public function Rules() {
        return array('Управление меню');
    }
    /**
     * Возвращает массив дочерних элементов конкретного меню
     * @global object $pdo
     * @param int $parent
     * @param int $menu_id
     * @return array()
     */
    public static function GetItems($parent = 0, $menu_id = 0) {
        global $pdo;
        $args[] = $parent;
        if ($menu_id) {
            $sql = ' AND menu_id = ?';
            $args[] = $menu_id;
        }
        $items = array();
        $q = $pdo->query("SELECT * FROM menu_items WHERE parent = ? {$sql} ORDER BY weight", $args);
        while ($menu_item = $pdo->fetch_object($q)) {
            $items[$menu_item->menu_item_id]['#item'] = $menu_item;
            $items[$menu_item->menu_item_id]['#child'] = Nav::GetItems($menu_item->menu_item_id);
        }
        return $items;
    }
    /**
     * Возвращает массив конкретного меню
     * @param int $menu_id
     * @return array()
     */
    public static function Get($menu_id) {
        $menu = Nav::GetItems(0, $menu_id);
        return $menu;
    }
    /**
     * Системно-вызываемый метод для создания блоков
     * @global object $pdo
     * @return array()
     */
    public static function BlockInfo() {
        global $pdo;
        $blocks = array();
        $q = $pdo->query("SELECT * FROM menu");
        while ($menu = $pdo->fetch_object($q)) {
            $blocks['block-menu-' . $menu->menu_id] = array(
                'title' => $menu->title,
                'content' => Nav::Render($menu->menu_id),
            );
        }
        return $blocks;
    }
    /**
     * Отрисовывет указаное меню
     * @param int $menu_id
     * @return str
     */
    public static function Render($menu_id) {
        return Theme::Render('navigation', Nav::Get($menu_id));
    }
    /**
     * Системно-вызываемый метод для создания стека темизации
     * @return array
     */
    public function Theme() {
        return array(
            'nav-item' => array(
                'type' => 'callback',
                'callback' => 'NavTheme::NavItem',
                'file' => Module::GetPath('Nav') . DS . 'NavTheme.class.php',
            ),
            'navigation' => array(
                'type' => 'callback',
                'callback' => 'NavTheme::Navigation',
                'file' => Module::GetPath('Nav') . DS . 'NavTheme.class.php',
            ),
        );
    }
    
    public static function GetContentSubForm($oContent = null){
        $path = Module::GetPath('nav') . DS . 'theme';
        $aVars = array();
        if($oContent->id){
            $item = Nav::GetByPath('content/'.$oContent->id);
            $aVars['name'] = $item->title;
            $aVars['weight'] = $item->weight;
            $aVars['parent'] = $item->parent;
            $aVars['menu_id'] = $item->menu_id;
            $aVars['item_id'] = $item->menu_item_id;
        }
        return Theme::Template($path . DS . 'content-subform.tpl.php',$aVars);
    }
    
    public static function GetMenuOptions($parent = 0,$menu_id = 0,$item_id = 0){
        global $pdo;
        $q = $pdo->query("SELECT * FROM menu");
        $out = '';
        while($menu = $pdo->fetch_object($q)){
            $options = self::_getMenuSubOtions($menu->menu_id, 0,':: ',$parent,$item_id);
            $out .= '<option value="menu-'.$menu->menu_id.'-0" '.(($parent == 0) && ($menu_id == $menu->menu_id)?'selected':'').' >'.$menu->title.'</option>'.$options;
        }
        return $out;
    }
    
    private static function _getMenuSubOtions($menu_id,$parent,$prefix = ':: ',$parent_set = 0,$item_id = 0){
        global $pdo;
        $q = $pdo->query("SELECT * FROM menu_items WHERE menu_id = ? AND parent = ?",array($menu_id,$parent));
        $options = '';
        while($mi = $pdo->fetch_object($q)){
            if($item_id == $mi->menu_item_id)
                continue;
            if($parent_set)
                $selected = ($parent_set == $mi->id?'selected':'');
            $options .= '<option value="menu-' . $menu_id . '-' . $mi->menu_item_id.'" '.$selected.' >' . $prefix . $mi->title.'</option>' . self::_getMenuSubOtions($menu_id, $mi->menu_item_id, $prefix.$prefix);
        }
        return $options;
    }
    
    public static function GetByPath($path){
        global $pdo;
        $path = Path::GetByAlias($path);
        $item = $pdo->QueryRow("SELECT * FROM menu_items WHERE path LIKE ?",array($path));
        return $item;
    }
}