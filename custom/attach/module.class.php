<?php

class Attach {
    
    public function Rules(){
        return array(
            'Скачивать файлы',
            'Видеть прикрепленные файлы',
            'Прикреплять файлы',
            'Настраивать прикрепление файлов'
        );
    }
    
    public function Init() {
        Event::Bind('ContentView','Attach::EventContentView');
        Event::Bind('FormLoad','Attach::EventFormLoad');
    }
    
    public function Menu(){
        return array(
            'files/download'=>array(
                'type'=>'callback',
                'callback'=>'Attach::PageDownload',
                'rules'=>array('Скачивать файлы'),
            ),
            'admin/attach/settings'=>array(
                'title'=>'Настройки',
                'callback'=>'Attach::PageSettings',
                'rules'=>array('Настраивать прикрепление файлов'),
            ),            
        );
    }
    
    public function Theme(){
        return array(
            'attached_files'=>array(
                'type'=>'template',
                'template'=>  Module::GetPath('attach') . DS . 'theme' . DS . 'attached-file.tpl.php',
                'arguments'=>array('files'=>array()),
            )
        );
    }
    
    public static function PageDownload(){
        $file_id = Path::Arg(2);
        $oFile = Attach::Load($file_id);
        $url = Path::Url($oFile->filepath);
        
        Core::Header('Content-Length', $oFile->size);
        Core::Header('Content-Disposition', 'attachment; filename="'.$oFile->original_name.'"');
        Core::Header('Content-type', 'application/force-download');
        Core::Header('Content-Disposition', 'attachment; filename="'.$oFile->original_name.'"');
        //Core::Header('Location', $url);
        echo file_get_contents($oFile->filepath);
        Core::Off();
    }
    
    public static function EventFormLoad(&$aForm){
        switch($aForm['id']){
            case 'content-add-form':
                $object = 'content';
                $object_id = $aForm['args'][0]->id;
                $content_types = Variable::Get('attach_types',array());
                $type = $aForm['args'][0]->type?$aForm['args'][0]->type:Path::Arg(3);
                if(!in_array($type, $content_types))
                        return;
                break;
        }
        Attach::GetSubForm($aForm,$object,$object_id);
    }
    
    public static function GetSubForm(&$aForm,$object,$object_id){
        if(!User::Access('Прикреплять файлы'))
            return;        
        $_SESSION['valid_extension'] = explode(" ",Variable::Get('attach_picture_ext','jpg jpeg png gif doc zip'));
        $forms = Attach::GetFormToAlter();
        if(!in_array($aForm['id'],$forms))
                return;
        $aFiles = Attach::GetFiles($object, $object_id);
        $aForm['content'][-1] = Theme::Render('file-upload','file2','Прикрепить файлы',$aFiles);
        $aForm['submit'][] = 'Attach::FormSubmit';        
    }
    
    public static function FormSubmit(&$aResult){
        global $pdo,$web_root;
        $files = $_SESSION['FILES'];
        if(is_array($_POST['delete_file']))
            foreach($_POST['delete_file'] as $filepath){
                $oFile = Attach::Load(ltrim($filepath,$web_root));
                Attach::Delete($oFile);
                //clean just uploaded by not saved files
                File::Delete(ltrim($filepath,$web_root));
                
            }
        if(!is_array($files))
            return;        
        $object_data = Path::Explode(Path::GetByAlias(trim($aResult['replace'],'/')));
        $object = $object_data[0];
        $object_id = $object_data[1];

        foreach($files as $file){
            Attach::Save($object, $object_id, $file);
        }
        unset($_SESSION['FILES']);
    }


    public static function GetFormToAlter(){
        return array(
            'content-add-form',
        );
    }
    
    public static function Save($object,$object_id,$oFile){
        global $pdo,$web_root;
        if(is_array($_POST['delete_file']))
            if(in_array($web_root . $oFile->filepath, $_POST['delete_file']))
                    return;
        $ext = explode(" ",Variable::Get('attach_picture_ext','jpg jpeg png gif doc zip'));

        if(!in_array(File::Ext($oFile->filepath), $ext))
                return Notice::Message('Загрузка данного типа файла запрещена.');
        $max_size = (Variable::Get('attach_picture_size',5 * 1024)) * 1024;
        if($oFile->size > $max_size)
            return Notice::Message('Файл превышает максимально допустимый размер.');
        $pdo->insert('attach_files',array(
           'created' => time(),
           'type'=>$oFile->type,
            'size'=>$oFile->size,
            'name'=>$oFile->name,
            'filepath'=>$oFile->filepath,
            'original_name'=>$oFile->original_name
        ));
        $file_id = $pdo->lastInsertId();
        if(!$file_id){
            Notice::Error('Загрузка файла не удалась');
            return;
        }
        $pdo->insert('attach',$data = array(
            'file_id'=>$file_id,
            'object'=>$object,
            'object_id'=>$object_id,
        ));
        return $data;
    }
    
    public static function EventContentView(&$args){
        $args['content']->data .= Attach::GetView('content',$args['content']->id);
    }
    
    public static function GetView($object,$object_id){
        global $pdo;
        if(!User::Access('Видеть прикрепленные файлы'))
            return;
        $aFiles = Attach::GetFiles($object, $object_id);
        if(!count($aFiles))
            return;
        $out = Theme::Render('attached_files',$aFiles);
        return $out;
    }
    
    public static function GetFiles($object,$object_id){
        global $pdo;
        $q = $pdo->query("SELECT af.* FROM attach a LEFT JOIN attach_files af ON a.file_id = af.id WHERE a.object LIKE ? AND a.object_id = ?",array($object,$object_id));
        $aFiles = array();
        while($file = $pdo->fetch_object($q))
            $aFiles[] = $file;
        return $aFiles;
    }
    
    public static function LoadById($fid){
        global $pdo;
        $oFile = $pdo->QueryRow("SELECT * FROM attach_files WHERE id = ?",array($fid));        
        return $oFile;
    }

    public static function LoadByFilepath($filepath){
        global $pdo;
        $oFile = $pdo->QueryRow("SELECT * FROM attach_files WHERE filepath LIKE ?",array($filepath));        
        return $oFile;
    }    
    
    public static function Load($fid){
        if(is_numeric($fid))
            $oFile = Attach::LoadById ($fid);
        elseif(is_string($fid))
            $oFile = Attach::LoadByFilepath ($fid);
        Event::Call('AttachFileLoad',$oFile);
        return $oFile;
    }
    
    public static function Delete($oFile){
        global $pdo;
        $pdo->query("DELETE FROM attach WHERE file_id = ?",array($oFile->id));
        $pdo->query("DELETE FROM attach_files WHERE id = ?",array($oFile->id));
        File::Delete($oFile->filepath);
    }
    
    public static function PageSettings(){
        return Form::GetForm('Attach::SettingsForm');
    }
    
    public static function SettingsForm(){
        return array(
            'id'=>'attach-settings-form',
            'standart'=>TRUE,
            'type'=>'template',
            'template'=>Module::GetPath('attach') . DS . 'forms' . DS . 'attach-settings-form.tpl.php',
        );
    }    
}