<?php

class Notification {

    public function Rules() {
        return array('Изменять состояние подписок');
    }

    public function Menu() {
        return array(
            'notification' => array(
                'title' => 'Управление рассылками',
                'callback' => 'Notification::MainPage',
                'rules' => array('Изменять состояние подписок'),
            ),
        );
    }

    public static function Send($key,$subject,$body, $to = false){
        global $pdo;
        if($to)
            return Notification::SendOne ($key, $to, $subject, $body);
        $q = $pdo->query("SELECT * FROM notification WHERE `key` LIKE ?",array($key));
        while($rec = $pdo->fetch_object($q))
            Notification::SendOne ($key, $rec->recipient, $subject, $body);
    }
    
    public static function SendOne($key, $recipient, $subject, $body) {
        if(!Notification::GetStatus($key, $recipient))
            return;
        Mail::Send('notificaton-'.$key, $recipient, $subject, $body);
    }

    public static function GetStatus($key,$recipient){
        $aList = Notification::GetList($recipient);
        if(!is_array($aList))
            return;
        return array_key_exists($key,$aList);
                
    }
    
    public static function GetFullList(){
        //Event::Bind('NotificationList', 'notification::ExampleList');//DELETE IT AFTER BUILD
        $aNotificationList = array();
        Event::Call('NotificationList', $aNotificationList);
        return $aNotificationList;
    }
    
    public static function MainPage() {
        $aNotificationList = Notification::GetFullList();
        return Form::GetForm('Notification::MainForm', $aNotificationList);
    }

    public static function MainForm() {
        return array(
            'id' => 'notification-form',
            'type' => 'template',
            'template' => Module::GetPath('notification') . DS . 'forms' . DS . 'notification-form.tpl.php',
            'submit' => array('Notification::MainFormSubmit'),
            'arguments' => array('aList' => array()),
        );
    }

    public static function MainFormSubmit(&$aResult) {
        global $pdo, $user;
        $aList = $aResult[1]; 
        $pdo->query("DELETE FROM notification WHERE recipient = ?", array($user->mail));
        $list = $_POST['list'] ? $_POST['list'] : array();
        foreach ($list as $key => $state) {
            if ($state == 'on') {
                $pdo->insert('notification', array(
                    'recipient' => $user->mail,
                    'key' => $key,
                    'title' => $aList[$key],
                ));
            }
        }
        Notice::Message('Настройки подписок сохранены');
    }

    public static function ExampleList(&$aList) {
        $aList['example_notify'] = 'Тестовая рассылка';
        $aList['some_notify'] = 'Еще одна рассылка';
    }

    public static function GetList($recipient) {
        global $pdo,$notification_table;
        if($notification_table)
            return $notification_table;
        $q = $pdo->query("SELECT * FROM notification WHERE recipient LIKE ?",array($recipient));
        $list = array();
        while($row = $pdo->fetch_object($q))
            $list[$row->key] = $row->title;
        return $GLOBALS['notification_table'] = $list;
    }

}