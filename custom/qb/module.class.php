<?php

class QB {
    public static function LoadFiles(){
        include_once Module::GetPath('QB') . DS . 'QBMisc.class.php';
    }
    
    public function Rules() {
        return array(
            'Использовать конструктор',
        );
    }

    public function Init() {
        include Module::GetPath('qb') . DS . 'QBEvent.class.php';
        Event::Bind('FormLoad', 'QBEvent::FormLoad');
        Event::Bind('QBQueryAlter','QBEvent::QBQueryAlter');
    }

    public function Menu() {
        return array(
            'admin/qbuilder' => array(
                'title' => 'Список конструкторов',
                'callback' => 'QBAdmin::MainPage',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
                'group'=>'Структура'
            ),
            'admin/qbuilder/add' => array(
                'title' => 'Создать конструктор',
                'callback' => 'QBAdmin::Add',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
                'group'=>'Структура'
            ),
            'admin/qbuilder/import' => array(
                'title' => 'Импортировать конструктор',
                'callback' => 'QBAdmin::Import',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
                'group'=>'Структура'
            ),            
            'admin/qbuilder/export' => array(
                'title' => 'Экспортировать конструктор',
                'type'  =>  'callback',
                'callback' => 'QBAdmin::Export',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
            ),            
            'admin/qbuilder/edit' => array(
                'title' => 'Редактировать',
                'type' => 'callback',
                'callback' => 'QBAdmin::Edit',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
            ),
            'admin/qbuilder/delete' => array(
                'type' => 'callback',
                'callback' => 'QBAdmin::Delete',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
            ),
            'ajax/qb-admin' => array(
                'type' => 'callback',
                'callback' => 'QBAdmin::Ajax',
                'file' => 'QBAdmin',
                'rules' => array('Использовать конструктор'),
            ),
            'qb'    =>  array(
                'type'  =>  'callback',
                'callback'=>'QBPages::Page',
                'file'  =>  'QBPages',
            )
        );
    }
    
    public static function BlockInfo(){
        global $pdo;
        $q = $pdo->query("SELECT * FROM qbuilder WHERE block = 1");
        $blocks = array();
        while($oQuery = $pdo->fetch_object($q)){
            $oQuery = QB::Load($oQuery->qbid);
            $prefix = QB::ControlLinks($oQuery);
            $blocks['block-qb-'.$oQuery->qbid] = array(
                'title' =>  $oQuery->name,
                'content'   => $prefix . QB::View($oQuery),
            );
        }
        return $blocks;
    }

    public static function Load($id) {
        global $pdo;
        $oQuery = $pdo->fetch_object($pdo->query("SELECT * FROM qbuilder WHERE qbid = ?", array($id)));
        if(!$oQuery->qbid)
            return false;
        $oQuery->query = $pdo->unserialize($oQuery->query);
        $oQuery->template = $pdo->unserialize($oQuery->template);
        $oQuery->rules = explode("\n",$oQuery->rules);
        foreach($oQuery->rules as &$rule)
            $rule = trim($rule);
        if(!$oQuery->template)
            $oQuery->template = QBMisc::DisplayTemplate();
        return $oQuery;
    }
    
    public static function Import($data){
        global $pdo;
        if(!is_array($data)){
            Notice::Error('Запрос не может быть импортирован. Неправильный формат данных');
            return false;
        }
        $pdo->insert('qbuilder',array(
            'name'      =>      $data['name'],
            'path'      =>      $data['path'],
            'block'     =>      $data['block'],
            'query'     =>      $pdo->serialize($data['query']),
            'template'  =>      $pdo->serialize($data['template']),
            'rules'     =>      implode("\n",$data['rules']),
        ));
        $id = $pdo->lastInsertId();
        $path = Path::AddAlias('qb/'.$id, $data['path']);
        if($path != $data['path'])
            $pdo->query("UPDATE qbuilder SET path = ? WHERE qbid = ?",array($path,$id));
        return QB::Load($id);
    }
    
    public static function View($oQuery){
        if(!$oQuery->qbid)
            return;

        self::LoadFiles();
        global $pdo;
        $query_args = array();
        $params = (object)array(
            'query' =>  $oQuery->query,
            'args'  =>  &$query_args,
        );
        
        

        Event::Call('QBQueryAlter',$params);
        
        $_query_args = array();
        foreach($query_args as $key=>$v)
            $_query_args[ltrim($key,':')] = $v;
        //var_dump($oQuery->query,$_query_args);
        $q = $pdo->query($oQuery->query,$_query_args);
        $display = new stdClass();
        $display->name = $oQuery->name;
        $display->path = $oQuery->path;
        $display->row = array();
        $i = 0;
        while($row = $pdo->fetch_object($q))
            $display->row['row_' . (++$i)] = $row;
        if($display->row['row_1'])
            $display->header = array_keys((array)$display->row['row_1']);
        ob_start();
        eval('?>' . $oQuery->template . '<?');
        $out = ob_get_contents();
	ob_end_clean();        
        return $out;
    }
    
    public static function ControlLinks($oQuery){
        if(User::Access('Использовать конструтор'))
            $out = Theme::Render('control-links',array(
               Theme::Render('link','admin/qbuilder/edit/'.$oQuery->qbid,'Редактировать'),
            )); 
        return $out;
    }
}