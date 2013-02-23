<?php

class AdminPages {

    public static function Frontpage() {
        global $pdo;
        $front = Variable::Get('site_frontpage');
        if (!$front)
            return '<p>Благодарим за установку <b>Skobka.CMS</b><br>Если вы еще не авторизовались, перейдите в <a href="/user">центр пользователя</a></p>'
		    .'<p>За дополнительными <a href="http://skobkacms.ru/modules-list">модулями и <a href="http://skobkacms.ru/themes-list">темами</a> оформления обращайтесь на сайт <a href="http://skobkacms.ru">http://skobkacms.ru</a></p>'
		    .'<p style="text-align:center"><b>Спасибо за Ваш интерес к SkobkaCMS</b></p>';
        else
            return Menu::Execute($front);
    }

    public static function Settings() {
        return Form::GetForm('AdminPages::SettingsForm');
    }

    public static function SettingsForm() {
        return array(
            'id' => 'site-settings-form',
            'type' => 'template',
            'standart' => TRUE,
            'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'site-settings-form.tpl.php',
	    'submit'=>array('AdminPages::SettingsFormSubmit'),
        );
    }
    
    public static function SettingsFormSubmit(&$aResult){
	Event::Call('CacheDelete');
    }
    
    public static function UrlAlias() {
        global $pdo;
        $q = $pdo->query("SELECT * FROM url_alias");
        $rows = array();
        while ($rec = $pdo->fetch_object($q))
            $rows[] = array(
                $rec->path,
                $rec->alias,
                Theme::Render('link-confirm', 'admin/url-alias/delete/' . $rec->uaid, 'Удалить'),
            );
        $table = Theme::Render('table', $rows, array('Путь', 'Псевдоним', 'Действия'));

        return $table . Form::GetForm('AdminPages::UrlAliasAdd');
    }

    public static function UrlAliasAdd() {
        return array(
            'id' => 'url-alias-main-form',
            'type' => 'template',
            'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'url-alias-main-form.tpl.php',
            'required' => array('path', 'alias'),
			'sisyphus'=>false,
            'submit' => array('AdminPages::UrlAliasAddSubmit'),
        );
    }

    public static function UrlAliasAddSubmit(&$aResult) {
        global $pdo;
        $pdo->insert('url_alias', array(
            'path' => $_POST['path'],
            'alias' => $_POST['alias'],
        ));
        Notice::Message('Псевдоним добавлен');
    }

    public static function UrlAliasDelete() {
        global $pdo;
        $id = Path::Arg(3);
        $pdo->query("DELETE FROM url_alias WHERE uaid = ?", array($id));
        Notice::Message('Псевдоним удален');
        Path::Back();
    }

    public static function Modules() {
        global $pdo;
        $aModule = Module::GetCustom();
        $out = '';
        $head = array(
            'Название', 'Версия', 'Описание', '',''
        );

        $q = $pdo->query("SELECT * FROM modules");
        while ($res = $pdo->fetch_object($q))
            $db_modules[$res->name] = $res;
        foreach ($aModule as $group => $modules) {
            $rows = array();
            foreach ($modules as $info) {
                $depends = AdminPages::ModuleGetDepends($info);
                $rows[] = array(
                    '#attributes' => array('class' => $db_modules[$info['module']]->status ? 'success' : ''),
                    $info['name'] ? $info['name'] : $info['module'],
                    $info['version'] ? $info['version'] : '--',
                    ($info['description'] ? $info['description'] : '--'),
                    $depends?:Theme::Render('link', 'admin/modules/toggle/' . $info['module'], $db_modules[$info['module']]->status ? 'Выключить' : 'Включить', array('class' => 'module-toggle')),
		    $db_modules[$info['module']]->status?Theme::Render('link-confirm','admin/modules/delete/'.$info['module'],'Удалить'):'',
                );
            }
            $out .= '<div class="module-group"><h6>' . $group . '</h6>' . Theme::Render('table', $rows, $head) . '</div>';
        }
        return $out;
    }

    public static function ModuleGetDepends($info){
        global $oEngine;
        if(!array_key_exists('depends', $info))
                return;
        $needs = array();
        foreach($info['depends'] as $module){
            if(!$oEngine->modules->{$module}){
                $needInfo = Admin::ModuleInfo($module);
                $needs[] = $needInfo['name']?:$module;
            }
        }
        if(!$needs)
            return false;
        return '<div class="module-depends">Требуется: <div class="module-depends-list">'.implode(', ',$needs).'</div></div>';
    }
    
    protected static function _ModuleToggle($module,$uninstall = false){
	global $pdo;
        $exists = false;
        foreach (Module::GetCustom() as $modules)
            foreach ($modules as $info)
                if ($info['module'] == $module) {
                    $exists = true;
                    break;
                }
        if (!$exists)
            return Path::Back();
        $oModule = $pdo->fetch_object($pdo->query("SELECT * FROM modules WHERE name LIKE ?", array($module)));
        if (!$oModule) {
            if (file_exists($sInstallFilie = Module::GetPath($module) . DS . 'module.install.php'))
                include $sInstallFilie;
            $pdo->insert("modules", array(
                'name' => $module,
                'status' => 1,
            ));
        }
        elseif ($oModule->id)
            $pdo->query("UPDATE modules SET status = ? WHERE id LIKE ?", array($oModule->status ? 0 : 1, $oModule->id));	
	if($uninstall){
	    if(File::Exists($sUninstallFile = Module::GetPath($module) . DS . 'module.uninstall.php'))
		    include $sUninstallFile;
	    $pdo->query("DELETE FROM modules WHERE name LIKE ?",array($module));
	}
    }
    
    public static function ModulesToggle() {
        $module = Path::Arg(3);
	self::_ModuleToggle($module);
        return Path::Back();
    }
    
    public static function ModulesDelete(){
	$module = Path::Arg(3);
	self::_ModuleToggle($module,true);
	Notice::Message('Модуль "'.$module.'" удален');
	return Path::Back();
    }
    
    public static function Cache(){
        Event::Call('CacheDelete');
        Path::Back();
    }

    public static function RunCron(){
        Event::Call('Cron');
        Path::Back();
    }
    
    public static function Update(){
        Path::Replace('update.php');
        Core::Off();
    }
    
    public static function FileUpload(){
        $ext = $_SESSION['valid_extension'];
        $ext = $ext?$ext:array('jpg','jpeg','gif','doc','png');
        $input_name = $_POST['input_name'];
        $files = File::SaveUpload($input_name);
        
        if($_SESSION['FILES'])
            foreach($_SESSION['FILES'] as $file)
                File::Delete ($file->filepath);
        $_SESSION['FILES'] = $files;
        
        foreach($files as $index=>$file){
            $f_ext = File::Ext($file->filepath);
            if(in_array($f_ext, $ext))
                $data .= Theme::Render ('file-upload-item',$file);
            else{
                File::Delete ($file->filepath);
                unset($files[$index]);
                Notice::Error('Файл '.$file->original_name.' не загружен т.к. имеет неверный формат');
            }
        }
        Core::Json(array('status'=>1,'files'=>$files,'data'=>$data));
    }
    
    public static function Robots(){
        $robots_rules = File::Exists('robots.txt')?file_get_contents('robots.txt'):'';
        return Form::GetForm('AdminPages::RobotsForm',$robots_rules);
    }
    
    public static function RobotsForm($text){
        return array(
            'id'=>'admin-robots-form',
            'fields'=>array(
                Theme::Render('input','textarea','text','Правила',$text,array('rows'=>30,)),
            ),
            'form-actions'=>array(
                'submit'=>array('text'=>'Сохранить'),
            ),
            'submit'=>array('AdminPages::RobotsFormSubmit'),
        );
    }
    
    public static function RobotsFormSubmit(&$aResult){
        file_put_contents('robots.txt', $aResult['POST']['text']);
        Notice::Message('Файл '.Theme::Render('link','robots.txt','robotx.txt').' сохранен');
    }
    
    public static function CheckUpdates(){
	Path::Replace('http://skobkacms.ru/download');
    }
}