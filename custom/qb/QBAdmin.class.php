<?php

class QBAdmin {
    private static function LoadFiles(){
        include Module::GetPath('QB') . DS . 'QBMisc.class.php';
        Theme::AddJs(Module::GetPath('QB') . DS . 'js' . DS . 'qb-admin.js');
        Theme::AddCss(Module::GetPath('QB') . DS . 'css' . DS .'qb-admin.css');
        jQueryUI::Load();
    }
    
    public static function MainPage(){
        global $pdo;
        $q = $pdo->query("SELECT * FROM qbuilder");
        $rows = array();
        while($query = $pdo->fetch_object($q))
            $rows[] = array(
                $query->qbid,
                Theme::Render('link',$query->path,$query->name),
                $query->path,
                $query->block?'Есть':"Нет",
                Theme::Render('link','admin/qbuilder/edit/'.$query->qbid,'Редактировать'),
                Theme::Render('link','admin/qbuilder/export/'.$query->qbid,'Экспорт'),
                Theme::Render('link-confirm','admin/qbuilder/delete/'.$query->qbid,'Удалить'),
            );
        $out = Theme::Render('table',$rows,array(
            'id','Запрос','Путь','Блок','Дейстия','',''
        ));
        $out .= '<div class="form-actions">'.Theme::Render('link','admin/qbuilder/import','Импортировать',array('class'=>'btn btn-primary')) .' '. Theme::Render('link','admin/qbuilder/add','Создать',array('class'=>'btn btn-success')).'</div>';
        return $out;
    }
    
    public static function Add(){
        self::LoadFiles();
        return Form::GetForm('QBAdmin::AddForm');
    }
    
    public static function Edit(){
        self::LoadFiles();
        $oQuery = QB::Load(Path::Arg(3));
        Notice::Message('Внимание! Использование конструктора может привести к порче существующего запроса.<br>Для внесения правок используйте поле Предпросмотр запроса.');
        return Form::GetForm('QBAdmin::AddForm',$oQuery);
    }
    
    public static function AddForm(){
        return array(
            'id'        =>  'qb-add-form',
            'type'      =>  'template',
            'template'  =>  Module::GetPath('QB') . DS . 'forms' . DS . 'qb-add-form.tpl.php',
            'ajax'      =>  false,
            'submit'    =>  array('QBAdmin::AddFormSubmit'),
            'arguments' =>  array('query'=>NULL,),
        );
    }
    
    public static function AddFormSubmit(&$aResult){
        global $pdo;
        $qb['name'] = $_POST['name'];
        $qb['path'] = $_POST['path'];
        $qb['block'] = $_POST['block'];
        $qb['query'] = $_POST['query'];
        $qb['template'] = $_POST['template'];   
        $qb['rules'] = $_POST['rules'];
        $aResult['qb'] = $qb;
        if(Path::Arg(2) == 'edit')
            return self::EditFormSubmit($aResult);
        foreach($qb as $k=>$v)if((!$v)&&($k != 'block')){
            Notice::Error('Недостаточно данных для создания.');
            return;
        }
        
        $pdo->insert('qbuilder',array(
            'name'  =>   $qb['name'],
            'path'  =>   $qb['path'],
            'block' =>   $qb['block'],
            'query' =>   $pdo->serialize($qb['query']),
            'template'=> $pdo->serialize($qb['template']),
            'rules' =>   $qb['rules'],
        ));
        Notice::Message('Запрос построен.');
        $qb_id = $pdo->lastInsertId();
        $pdo->insert('url_alias',array(
            'path' =>    'qb/'.$qb_id,
            'alias'=>    $qb['path'],
        ));
        $aResult['replace'] = 'admin/qbuilder';
    }
    
    public static function EditFormSubmit(&$aResult){
        global $pdo;
        $qb = $aResult['qb'];
        $id = Path::Arg(3);
        $oQuery = QB::Load($id);
        if(!$oQuery->qbid)
            return;
        
        $q = $pdo->query("UPDATE qbuilder SET name = ?, path = ?, block = ?, query = ?, template = ?, rules = ? WHERE qbid = ?",array(
            $qb['name'],
            $qb['path'],
            $qb['block'],
            $pdo->serialize($qb['query']),
            $pdo->serialize($qb['template']),
            $qb['rules'],
            $id,
        ));
        
        $pdo->query("DELETE FROM url_alias WHERE alias = ?",array($oQuery->path));
        $pdo->insert('url_alias',array(
           'path' => 'qb/'.$id,
           'alias'=>$qb['path'],
        ));
        
        Notice::Message('Запрос сохранен');
        $aResult['replace'] = 'admin/qbuilder';
    }
    
    public static function Ajax(){
        self::LoadFiles();
        $data = array();
        $data['op'] = Path::Arg(2);
        switch(Path::Arg(2)){
            case 'get-fields':
                $tables = explode(",",$_POST['tables']);
                $fields = array();
                foreach($tables as $table)
                    $fields[] = QBMisc::Fields ($table);
                $data['fields'] = $fields;
                break;
            
        }
        Core::Json($data);
    }
    
    public static function Delete(){
        global $pdo;
        $id = Path::Arg(3);
        $oQuery = QB::Load($id);
        Event::Call('QBDelete',$oQuery);
        $pdo->query("DELETE FROM qbuilder WHERE qbid = ?",array($id));
        Path::DeleteAlias($oQuery->path, true);
        Notice::Message('Запрос удален');
        Path::Back();
    }
    
    public static function Export(){
        self::LoadFiles();
        $oQuery = QB::Load(Path::Arg(3));
        if(!$oQuery->qbid){
            Notice::Error('Запрос не существует');
            return Path::Back();
        }
        return '<textarea id="qb-export">return '.var_export((array)$oQuery,1).';</textarea><div class="form-actions"><a href="javascript:window.history.back(-1);" class="btn btn-success">Готово</a></div>';
    }
    
    public static function Import(){
        return Form::GetForm('QBAdmin::ImportForm');
    }
    
    public static function ImportForm(){
        return array(
            'id'        =>  'qb-import-form',
            'type'      =>  'callback',
            'callback'  =>  'QBAdmin::ImportFormCallback',
            'submit'    =>  array('QBAdmin::ImportFormSubmit'),
            'required'  =>  array('import_data'),
        );
    }
    
    public static function ImportFormCallback(){
        return array(
            Theme::Render('input','textarea','import_data','Данные для импорта'),
            Theme::Render('form-actions',array(
                'submit'=>array('text'=>"Импортировать"),
            )),
        );
    }
    
    public static function ImportFormSubmit(&$aResult){
        QB::Import(eval($_POST['import_data']));
        Notice::Message('Запрос импортирован');
    }
}
