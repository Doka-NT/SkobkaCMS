<?php

class AdminPages {

    public static function Frontpage() {
        global $pdo;
        $front = Variable::Get('site_frontpage');
        if (!$front)
            return 'Благодарим за установку <b>Skobka.CMS</b><br>Если вы еще не авторизовались, перейдите в <a href="/user">центр пользователя</a>';
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
        );
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
            'Название', 'Версия', 'Описание', '',
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
    
    public static function ModulesToggle() {
        global $pdo;
        $module = Path::Arg(3);
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
        return Path::Back();
    }
    
    public static function Cache(){
        Event::Call('CacheDelete');
        Path::Back();
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
}