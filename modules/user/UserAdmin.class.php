<?php

class UserAdmin {

    public static function PageGroups() {
        global $pdo;
        if ($gid = (int) Path::Arg(3)){
            $group = User::LoadGroup($gid);
            Theme::SetTitle('Настройка прав группы '.$group->name);
            return Form::GetForm('UserAdmin::PageGroupForm', $group);
        }
        $q = $pdo->query("SELECT * FROM user_groups");
        $head = array(
            'Id',
            'Название'
        );
        $row = array();
        while ($group = $pdo->fetch_object($q)) {
            $row[] = array(
                $group->gid,
                Theme::Render('link', 'admin/user/groups/' . $group->gid, $group->name),
            );
        }
        return Theme::Render('table', $row, $head) . Form::GetForm('UserAdmin::GroupForms');
    }

    public static function GroupForms() {
        return array(
            'id' => 'user-group-add-form',
            'type' => 'callback',
            'callback' => 'UserAdmin::GroupFormsBuilder',
            'required' => array('name'),
            'submit' => array('UserAdmin::GroupFormsSubmit'),
        );
    }

    public static function GroupFormsBuilder() {
        return array(
            Theme::Render('input', 'text', 'name', 'Добавить новую группу'),
            Theme::Render('form-actions', array(
                'submit' => array('text' => 'Добавить'),
            )),
        );
    }

    public static function GroupFormsSubmit(&$aResult) {
        global $pdo;
        $pdo->insert('user_groups', array(
            'name' => $_POST['name'],
        ));
        Notice::Message('Группа добавлена');
    }

    public static function PageGroupForm($group) {
        return array(
            'id' => 'user-group-form',
            'type' => 'template',
            'template' => Module::GetPath('user') . DS . 'theme' . DS . 'user-group-form.tpl.php',
            'arguments' => array('group' => $group),
            'submit' => array('UserAdmin::GroupFormSubmit'),
        );
    }

    public static function GroupFormSubmit(&$aResult) {
        if (!$_POST['rules'])
            $_POST['rules'][Path::Arg(3)] = array();
        global $pdo;
        $RULES = array();
        foreach ($_POST['rules'] as $gid => $aRules) {
            foreach ($aRules as $rule => $state)
                if ($state == 'on')
                    $RULES[] = $rule;
            $pdo->query("UPDATE user_groups SET rules = ? WHERE gid = ?", array(
                $pdo->serialize($RULES),
                $gid,
            ));
        }
        Notice::Message('Правила сохранены');
    }

    public static function UserList(){
        global $pdo;
	$per_page = 20;
        $q = $pdo->PagerQuery($sql = "SELECT * FROM users",null,$per_page);
        $row = array();
        while($u = $pdo->fetch_object($q))
            $row[] = array(
                $u->uid,
                Theme::Render('link','user/'.$u->uid,$u->name),
                User::LoadGroup ($u->gid)->name,
                Theme::Render('link','user/'.$u->uid.'/edit','Редактировать профиль'),
            );
        return Theme::Render('table',$row,array('uid','Логин',"Группа","Действия")) . Theme::Pager($sql, NULL, $per_page);
    }
}