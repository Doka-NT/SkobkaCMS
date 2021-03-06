<?php

class User {

    public function __construct() {
        Event::Bind('UserLogin', 'User::EventUserLogin');
    }

    public function Rules() {
        return array('Управление пользователями');
    }

    public function Menu() {
        return array(
            'user' => array(
                'title' => 'Центр пользователя',
                'callback' => 'User::PageUser',
                'group'=>'Пользователи'
            ),
            'admin/user/groups' => array(
                'title' => 'Группы пользователей',
                'callback' => 'UserAdmin::PageGroups',
                'file' => 'UserAdmin',
                'rules' => array('Управление пользователями'),
                'group'=>'Пользователи'
            ),
            'admin/user/list' => array(
                'title' => 'Список пользователей',
                'callback' => 'UserAdmin::UserList',
                'file' => 'UserAdmin',
                'rules' => array('Управление пользователями'),
                'group'=>'Пользователи'
            ),
            'logout' => array(
                'type' => 'callback',
                'callback' => 'User::Logout',
            ),
        );
    }

    public static function Access($sRules) {
        if (is_string($sRules))
            $sRules = array($sRules);
        global $user;
        if (!$user->uid) {
            if(!$user) {
                $user = new stdClass ();
                $user->uid = 0;
            }
            $user->group = User::LoadGroup(1);
        }
        if ($user->uid == 1)
            return true;
        $aRules = $user->group->rules;
        $bool = false;
        if (!is_array($sRules))
            $sRules = array($sRules);
        foreach ($sRules as $rule)
            if (in_array($rule,$aRules))
                $bool = true;

        return $bool;
        //$aMenuItem = Menu::GetActiveItem();
    }

    public static function Load($uid) {
        global $pdo;
        $account = $pdo->fetch_object($pdo->query("SELECT * FROM users WHERE uid = ?", array($uid)));
        if ($account) {
            if (!$account->gid)
                $account->gid = $account->uid ? 2 : 1; //Set group guests or users
            $account->group = self::LoadGroup($account->gid);
            Event::Call('UserLoad',$account);
        }
        return $account;
    }

    public static function BlockInfo() {
        global $user;
        return array(
            'block-user-auth' => array(
                'title' => $user->uid ? 'Меню пользователя ' . $user->name : 'Авторизация на сайте',
                'content' => Form::GetForm('User::AuthForm'),
            ),
        );
    }

    public static function AuthForm() {
        global $session, $user;
        $auth_form = array(
            'id' => 'user-auth',
            'type' => 'template',
            'template' => Module::GetPath('User') . DS . 'theme' . DS . 'user-auth.tpl.php',
            'required' => array('name', 'password'),
            'submit' => array('User::AuthFormSubmit'),
            //'ajax'=>true,
			'sisyphus'=>false,
            'result' => 'prepend',
        );
        if($user->uid){
            $links = array();
            Event::Call('UserMenuLinks',$links);
            $links = '<li>'.implode('</li><li>',$links).'</li>';
        }
        $user_menu = array(
            'id' => 'user-menu',
            'type' => 'template',
            'sisyphus'=>false,
            'arguments'=>array('links'=>$links),
            'template' => Module::GetPath('user') . DS . 'theme' . DS . 'user-menu.tpl.php',
        );

        if (!$user->uid)
            return $auth_form;
        else {
            if (!$user)
                return $auth_form;
            return $user_menu;
        }
    }

    public static function AuthFormSubmit(&$aResult, $aForm) {
        global $pdo;
        $q = $pdo->query("SELECT * FROM users u WHERE (u.name = ? OR u.mail = ?) AND u.password = MD5(?)", array(
            $_POST['name'],
            $_POST['name'],
            $_POST['password'],
                ));
        $user = $pdo->fetch_object($q);
        if (!$user) {
            Notice::Error('Не правильное имя пользователя или пароль.<br>Если Вы забыли пароль, перейди в ' . Theme::Render('link', 'user/password', 'центр восстановления аккаунта'));
            $user = User::UserGuest();
        } else {
            $aResult['replace'] = 'frontpage';
            if ($aForm['ajax'])
                Notice::Message('Сейчас вы будете перемещены');
        }
        $GLOBALS['user'] = $user;
        Event::Call('UserLogin');
    }

    public static function EventUserLogin() {
        global $user, $salt, $pdo;
        if (!$user->uid)
            return;
        $time = time();
        $session = array(
            'name' => md5($time . $user->name . $user->password . $salt),
            'value' => md5($salt . $time . $user->name . $user->password . $salt),
            'uid' => $user->uid,
        );
        //$pdo->insert('session',$session);
        Session::Set($session);
        setcookie('__SESSID', $session['name'], time() + (60 * 60 * 24 * 30 * 365), '/');
        //Path::Replace('frontpage');
    }

    public static function AuthCookie() {
        global $pdo;
        $sessid = $_COOKIE['__SESSID'];
        //$session = $pdo->fetch_object($pdo->query("SELECT * FROM session WHERE name LIKE ?",array($sessid)));
        $session = Session::Get($sessid);
        if (!$session)
            return false;
        $user = User::Load($session->uid);
        if (!$user)
            $user = User::UserGuest();
        $GLOBALS['user'] = $user;
        return $session;
    }

    public static function UserGuest() {
        return (object) array(
                    'uid' => 0,
                    'name' => 'Гость',
        );
    }

    public static function Logout() {
        self::_Logout();
        Path::Replace('frontpage');
    }

    private static function _Logout() {
        $sessid = $_COOKIE['__SESSID'];
        Session::Delete($sessid);
        setcookie('__SESSID', null, time() - (60 * 60 * 24 * 30 * 365), '/');
    }

    public static function LoadProfile($account){
        Event::Call('ProfileLoad',$account);
		$account->fields = is_array($account->fields)?$account->fields:array();
        ksort($account->fields);
        return $account;
    }
    
    public static function PageUser() {
        global $user;
        if ((Path::Arg(1) == 'password') && (!$user->uid))
            return Form::GetForm('User::ResetPassword');
        if ((Path::Arg(1) == 'register') && (!$user->uid))
            return Form::GetForm('User::Register');
        elseif ((Path::Arg(1) == 'password') && ($user->uid))
            return Menu::NotFound();
        if (!$user->uid && ( !Path::Arg(1) || (Path::Arg(1) == 'login')))
            return Form::GetForm('User::AuthForm');
        if (Path::Arg(2) == 'edit'){
            if(User::Access('Управление пользователями') || (Path::Arg(1) == $user->uid)){
		$account = User::Load(Path::Arg(1));
		if(!$account->uid)
		    return Menu::NotFound ();
		Theme::SetTitle('Профиль пользователя '.$account->name);
                return Form::GetForm('User::EditForm');
	    }
            else
                return Menu::NotFound();
        }
        if ($uid = Path::Arg(1))
            $account = User::Load($uid);
        else
            $account = $user;
	Theme::SetTitle('Профиль пользователя '.$account->name);
        $aVars = array('account'=>User::LoadProfile($account));
        $prefixTemplate = $suffixTemplate = '';
        $p = array(&$prefixTemplate,$account);
        Event::Call('UserPrefixTemplate',$p);
        $p2 = array(&$suffixTemplate,$account);
        Event::Call('UserSuffixTemplate',$p2);
        $aVars['prefix'] = $prefixTemplate;
        $aVars['suffix'] = $suffixTemplate;
        return Theme::Template(Module::GetPath('user') . DS . 'theme' . DS . 'account.tpl.php', $aVars);
    }

    public static function EditForm() {
        $account = User::Load(Path::Arg(1));
        if (!$account) {
            Path::Replace('404');
            // return array('id'=>'edit-user-form');
        }
        return array(
            'id' => 'edit-user-form',
            'type' => 'template',
            'template' => Module::GetPath('user') . DS . 'theme' . DS . 'edit-user-form.tpl.php',
            'arguments' => array('account' => $account),
            'submit' => array('User::EditFormSubmit'),
        );
    }

    public static function CheckAccess($uid) {
        if ((User::Access('Редактировать профиль') || ($uid == $GLOBALS['user']->uid)) && (Path::Arg(0) == 'user'))
            return true;
        return false;
    }

    public static function EditFormSubmit(&$aResult) {
        global $user, $pdo;
        $uid = Path::Arg(1);
        if (!$uid)
            return;
        $account = User::Load($uid);
        if (User::CheckAccess($uid)) {

            $acc = $_POST['user'];
            if (!$acc['password'])
                $pdo->query("UPDATE users SET mail = ?, gid = ? WHERE uid = ?", array($acc['mail'], $acc['group'], $account->uid));
            else {
                $pdo->query("UPDATE users SET mail = ?, password = MD5(?), gid = ? WHERE uid = ?", array(
                    $acc['mail'],
                    $acc['password'],
                    $acc['group'],
                    $account->uid,
                ));
                if ($user->uid == $acc->uid) {
                    self::_Logout();
                    $acc = $GLOBALS['user'] = User::Load($acc->uid);
                    self::EventUserLogin();
                }
                Notice::Message('Пароль изменен');
                Event::Call('UserPasswordChange', $acc);
            }
            $acc = User::Load($acc->uid);
            Notice::Message('Изменения сохранены');
            Event::Call('UserEdit', $acc);
        }
    }

    public static function GeneratePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    public static function ResetPassword() {
        Theme::SetTitle('Восстановление пароля');
        return array(
            'id' => 'user-reset-password-form',
            'type' => 'template',
			'sisyphus'=>false,
            'template' => Module::GetPath('user') . DS . 'theme' . DS . 'user-reset-password-form.tpl.php',
            'required' => array('find'),
            'submit' => array('User::ResetPasswordSubmit'),
        );
    }

    public static function ResetPasswordSubmit(&$aResult) {
        global $pdo;
        $find = $_POST['find'];
        $res = $pdo->fetch_object($pdo->query("SELECT * FROM users WHERE name LIKE :find OR mail LIKE :find", array('find' => $find)));
        if (!$res->uid)
            return Notice::Error('Указанного пользователя не существует');

        $password = User::GeneratePassword();
        $pdo->query("UPDATE users SET password = MD5(?) WHERE uid = ?", array($password, $res->uid));
        Mail::Send('user-password-reset', $res->mail, "Восстановление пароля", 'Ваш новый пароль:<br><b>' . $password . '</b>');
        Notice::Message('Инструкции по восстановлению пароля отправлены на указанный при регистрации почтовый ящик.');
        $aResult['replace'] = 'frontpage';
    }

    public static function Register() {
        Theme::SetTitle('Регистрация');
        return array(
            'id' => 'user-register-form',
            'type' => 'template',
            'template' => Module::GetPath('user') . DS . 'theme' . DS . 'user-register.tpl.php',
            'reguired' => array('name', 'mail', 'password'),
            'submit' => array('User::RegisterSubmit'),
        );
    }

    public static function RegisterSubmit(&$aResult) {
        $rec = $_POST;
        if ($rec['password'] != $rec['password_2'])
            return Notice::Error('Указанные пароли не совпадают.');
        if (!preg_match('/^[a-zA-Zа-яА-Я_\d][-a-zA-Zа-яА-Я0-9_\.\d]*\@[a-zA-Zа-яА-Я\d][-a-zA-Zа-яА-Я\.\d]*\.[a-zA-Zа-яА-Я]{2,4}$/', $rec['mail']))
            return Notice::Error('Указан не верный адрес эл.почты.');
        if (!$rec['name'])
            return Notice::Error('Необходимо указать имя пользователя');
        global $pdo;
        $res = $pdo->fetch_object($pdo->query("SELECT COUNT(*) as total FROM users WHERE name LIKE :name OR mail LIKE :mail", array(
                    'name' => $rec['name'],
                    'mail' => $rec['mail'],
                )));
        if ($res->total)
            return Notice::Error('Пользователь с таким логином или почтой уже зарегистрирован.');
        $pdo->insert('users', array(
            'gid' => 2,
            'name' => strip_tags($rec['name']),
            'mail' => $rec['mail'],
            'password' => md5($rec['password']),
        ));
        $uid = $pdo->lastInsertId();
        $user = User::Load($uid);
        self::_Logout();
        $GLOBALS['user'] = $user;
        self::EventUserLogin();
        Event::Call('UserRegister', $user);
        Notice::Message('Вы только что зарегистрировались и вошли на сайт.');
        $aResult['replace'] = 'frontpage';
    }

    public static function GetAllRules() {
        global $oEngine;
        $aRules = array();
        foreach ($oEngine->modules as $module){
            $aRules[get_class($module)] = array();
            if (method_exists($module, 'Rules'))
                $aRules[get_class($module)] += $module->Rules();
            if(is_array($module->aRules))
                $aRules[get_class($module)] += $module->aRules;
        }
        return $aRules;
    }

    public static function LoadGroup($gid = false) {
        global $pdo;
        if (!$gid) {
            $groups = array();
            $q = $pdo->query("SELECT * FROM user_groups");
            while ($g = $pdo->fetch_object($q))
                $groups[$g->gid] = $g;
            return $groups;
        }
        $group = $pdo->fetch_object($pdo->query("SELECT * FROM user_groups WHERE gid = ?", array($gid)));
        if ($group->rules)
            $group->rules = $pdo->unserialize($group->rules);
        else
            $group->rules = array();
        return $group;
    }

}