<?php

class NavEvent {

    public static function EventFormLoad(&$aForm) {
        if ($aForm['id'] == 'content-add-form') {
            $oContent = $aForm['args'][0];
            $aForm['content'][-1000] = Nav::GetContentSubForm($oContent);
            $aForm['submit'][] = 'NavEvent::ContentAddSubmit';
        }
    }

    public static function ContentAddSubmit(&$aResult) {
        global $pdo;
        $item = $_POST['menu'];
        $str_parse = explode("-", $item['parent']);
        $item['menu_id'] = $str_parse[1];
        $item['parent'] = $str_parse[2];
        $item['path'] = 'content/' . $aResult['content_id'];
        if(!$item['name'])
            return;
        if ($item['menu_item_id'])
            $pdo->query("UPDATE menu_items SET parent = ?, title = ?, menu_id = ?, weight = ? WHERE path LIKE ?", array(
                $item['parent'],
                $item['name'],
                $item['menu_id'],
                $item['weight'],
                $item['path'],
            ));
        else
            $pdo->insert('menu_items', array(
                'title' => $item['name'],
                'path' => $item['path'],
                'parent' => (int) $item['parent'],
                'menu_id' => $item['menu_id'],
                'weight'=>$item['weight'],
            ));
    }

}