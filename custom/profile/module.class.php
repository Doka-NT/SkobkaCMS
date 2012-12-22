<?php

class profile {
    public function Init(){
        Event::Bind('FormLoad', 'Profile::EventFormLoad');
        Event::Bind('UserLoad', 'Profile::EventUserLoad');
        Event::Bind('UserPrefixTemplate','Profile::EventUserPrefixTemplate');
    }
    
    public function Theme(){
        return array(
            'profile-picture'=>array(
                'type'=>'callback',
                'callback'=>'profile::ThemeUserPicture',
                'arguments'=>array('user'=>NULL),
            ),
        );
    }
    
    public function Rules(){
        return array(
            'Просмотр профилей','Настройка профилей','Загружать изображение'
        );
    }
    
    public function Menu(){
        return array(
            'admin/profile'=>array(
                'title'=>'Настройки профилей',
                'callback'=>'ProfileAdmin::Settings',
                'file'=>'ProfileAdmin',
                'rules'=>array('Настройка профилей'),
            ),
            'profile/file_upload'=>array(
                'type'=>'callback',
                'callback'=>'profile::FileUpload',
                'rules'=>array('Загружать изображение'),
            ),
        );
    }
    
    public static function EventFormLoad(&$aForm){
        if($aForm['id'] != 'edit-user-form')
            return;
        $sPath = Module::GetPath('profile');
        Theme::AddCss($sPath . DS . 'css' . DS . 'profile.css');
        Theme::AddJs($sPath . DS . 'js' . DS . 'profile.js');
        Theme::AddJs($sPath . DS . 'js' . DS . 'jquery.upload5.js');
        jQueryUI::Load();
        
        $aForm['sisyphus'] = false; 
        $aForm['content'][-1] = Theme::Template($sPath . DS . 'forms' . DS . 'edit-user-form.tpl.php', $aForm);
        $aForm['submit'][] = 'profile::EditUserFormSubmit';
    }
    
    public static function EventUserLoad(&$account){
        if(!User::Access('Просмотр профилей'))
            return;        
        $account = profile::Load($account);
    }
    
    public static function EditUserFormSubmit(&$aResult){
        $account = User::Load(Path::Arg(1));
        foreach($_POST['profile'] as $k=>$v)
            $account->profile->$k = $v;
        $account->profile->birthday = date_create_from_format('d.m.Y',$account->profile->birthday)->getTimestamp();
        $account->profile->homepage = 'http://' . preg_replace('/(.*):\/\//','',$account->profile->homepage);
        profile::SaveData($account);
    }
    
    public static function ThemeUserPicture($account){
        if(!User::Access('Просмотр профилей'))
            return;        
        if($account->profile->picture)
            return '<img src="' . Path::Url($account->profile->picture) . '?'.time().'" class="user-picture" alt="Изображение пользователя" title="' . $account->name .'"/>';
    }
    
    public static function FileUpload(){
        $types = Variable::Get('profile_picture_ext','jpg jpeg png gif');
        $userpic_dir = 'files/' . Variable::Get('profile_picture_dir','userpic');
        $file_type = explode('/',$_FILES['files']['type'][0]);
        $max_size = Variable::Get('profile_picture_size',2 * 1024);
        if(!is_array($file_type) || (count($file_type) != 2))
            exit;
        if(($file_type[0] != 'image') && (strstr($types,$file_type[1]) === false))
                Core::Json (array('status'=>0,'fmessage'=>'Файл имеет недопустимый формат'));
        
        global $user,$pdo;
        $tmp_file = $_FILES['files']['tmp_name'][0];
        $filesize = filesize($tmp_file) / 1024;
        if($filesize > $max_size)
            Core::Json (array('status'=>0,'fmessage'=>'Размер файла не должен превышать '.floor($max_size).'kb. <br> Вы загрузили файл размером ' . floor($filesize) .'kb',));
        
        $filename = 'userpicture-'.$user->uid . '.' . $file_type[1];
        $filepath = $userpic_dir . DS . $filename;
        if(!$user->profile)
            $user->profile = (object)array();
        if(File::Move($tmp_file,$filepath)){
            if($user->profile->picture)
                File::Delete ($user->profile->picture);
            profile::PreparePicture($filepath);
            $user->profile->picture = $filepath;
            profile::SaveData($user);            
            Core::Json(array(
                'status' => 1,
                'picture'=>Theme::Render('profile-picture',$user),
            ));
        }
        Core::Json(array(
            'status'=>0,
        ));
    }
    
    public static function PreparePicture($filepath){
        $size = explode("x",Variable::Get('profile_picture_wh','300x300'));
        $w = $size[0]; $h = $size[1];
        $image = Image::ResizeCrop($filepath, $w, $h);
        if(Image::Save($image,$filepath))
            return $filepath;
    }
    
    public static function SaveData($account){
        global $pdo;
        $check = $pdo->fetch_object($pdo->query("SELECT COUNT(*) as total FROM profile WHERE uid = ?",$account->uid))->total;
        if(!$check)
            $pdo->insert('profile',array(
               'uid'=>$account->uid,
               'picture'=>$account->profile->picture,
               'surname'=>$account->profile->surname,
               'name'=>$account->profile->name,
               'middlename'=>$account->profile->middlename,
               'birthday'=>$account->profile->birthday,
               'homepage'=>$account->profile->homepage
            ));
        else
            $pdo->query("UPDATE profile SET 
                `picture` = :picture, 
                `surname` = :surname,
                `name` = :name,
                `middlename` = :middlename,
                `birthday` = :birthday,
                `occupation` = :occupation,
                `homepage` = :homepage
            WHERE uid = :uid ",array(
                'picture'=>$account->profile->picture,
                'surname'=>$account->profile->surname,
                'name'=>$account->profile->name,
                'middlename'=>$account->profile->middlename,
                'birthday'=>$account->profile->birthday,
                'occupation'=>$account->profile->occupation,
                'homepage'=>$account->profile->homepage,
                'uid'=>$account->uid,
            ));
    }
    
    public static function Load($account){
        global $pdo;
        if(!User::Access('Просмотр профилей'))
            return;        
        $profile = $pdo->fetch_object($pdo->query("SELECT * FROM profile WHERE uid = ?",array($account->uid)));
        $account->profile = $profile;
        return $account;
    }
    
    public static function EventUserPrefixTemplate(&$params){
        if(!User::Access('Просмотр профилей'))
            return;
        Theme::AddCss(Module::GetPath('profile') . DS . 'css' . DS . 'profile.css');
        $vars = array('account'=>  profile::Load($params[1]));
        $params[0] .= Theme::Template(Module::GetPath('profile') . DS . 'theme' . DS . 'profile-account.tpl.php', $vars);    
    }
}