<?php

class tinymce {

    public static function Load($init = true) {
        Theme::AddJs(Module::GetPath('tinymce') . DS . 'tiny_mce' . DS . 'tiny_mce.js');
        Event::Bind('PagePreRender', 'tinymce::EventPagePreRender');
        if ($init)
            Theme::AddJs(Module::GetPath('tinymce') . DS . 'init.js');
    }

    public static function EventPagePreRender() {
        Theme::PackCss(); //To tell tiny mce about our styles
    }

}