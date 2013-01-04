<?php

class GalleryEvent {

    public static function ContentLoad(&$oContent) {
        $types = Variable::Get('gallery_types', array());
        if (!$types)
            return;
        if (!in_array($oContent->type, $types))
            return;
        $oContent->is_gallery = true;
        $oContent->attach_skip = explode(" ", Variable::Get('gallery_ext', 'jpeg jpg png'));
        $oContent->content[-100] = Gallery::View($oContent);
    }

    public static function FormLoad(&$aForm) {
        $_SESSION['valid_extension'] = explode(" ", Variable::Get('gallery_ext', 'jpeg jpg png'));
    }

}