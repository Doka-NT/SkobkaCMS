<?php

class Admin {

    public function __construct() {
        Event::Bind('Loaded', 'Admin::EventLoaded');
        Event::Bind('PdoQuery', 'Admin::EventPdoQuery');
        Event::Bind('CacheDelete', 'Admin::EventCacheDelete');
        Event::Bind('FormLoad', 'Admin::EventFormLoad');
    }

    public static function EventFormLoad(&$aForm) {
        Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'sisyphus.js');
        if ($aForm['sisyphus'] || !array_key_exists('sisyphus', $aForm))
            Theme::AddJsSettings(array(
                'forms' => array(
                    'sisyphus' => array($aForm['id'] => true),
                ),
            ));
        if ($_SESSION['sisyphus'][$aForm['id']]) {
            Theme::AddJsSettings(array(
                'forms' => array(
                    'sisyphus' => array($aForm['id'] => false),
                ),
            ));
            unset($_SESSION['sisyphus'][$aForm['id']]);
        }
        if(!$aForm['submit'])
            $aForm['submit'] = array();
        if($aForm['submit']){
            if(!is_array($aForm['submit']))
                $aForm['submit'] = array($aForm['submit']);
        }
        $aForm['submit'][] = 'Admin::EventFormLoadSubmit';
    }

    public static function EventFormLoadSubmit(&$aResult,$aForm){
        $_SESSION['sisyphus'][$aForm['id']] = true;        
    }
    
    public static function EventPdoQuery($opt = array()) {
        $sql = $opt['sql'];
        $args = $opt['args'];

        $GLOBALS['query_counter']++;
        $GLOBALS['query_list'][] = $sql . ($args ? ' <i>(' . implode(',', (array) $args) . ')</i>' : '');
    }

    public function Rules() {
        return array('Настройка сайта', 'Просмотр статистики выполнения');
    }

    public static function EventLoaded() {
        global $theme;

        Theme::AddCss(Theme::GetPath('default') . DS . 'css/reset.css');
        Theme::AddCss(Theme::GetPath('default') . DS . 'bootstrap/css/bootstrap.min.css');
        Theme::AddCss(Theme::GetPath('default') . DS . 'css/base.css');
        Theme::AddJs(Theme::GetPath('default') . DS . 'js/jquery-1.8.2.min.js');
        Theme::AddJs(Theme::GetPath('default') . DS . 'bootstrap/js/bootstrap.min.js');
        Theme::AddJs(Theme::GetPath('default') . DS . 'js/main.js');

        Theme::SiteName($s = Variable::Get('site_name'));
    }

    public function Menu() {
        return array(
            'frontpage' => array(
                'callback' => 'AdminPages::Frontpage',
                'file' => 'AdminPages',
                'title' => 'Главная',
            ),
            'admin/cache' => array(
                'title' => 'Сбросить кеш',
                'callback' => 'AdminPages::Cache',
                'file' => 'AdminPages',
                'rules' => array('Настройка сайта'),
            ),
            'admin/settings' => array(
                'callback' => 'AdminPages::Settings',
                'file' => 'AdminPages',
                'title' => 'Настройки',
                'rules' => array('Настройка сайта'),
            ),
            'admin/url-alias' => array(
                'title' => 'URL пути (ЧПУ)',
                'file' => 'AdminPages',
                'rules' => array('Настройка сайта'),
                'callback' => 'AdminPages::UrlAlias',
            ),
            'admin/url-alias/delete' => array(
                'type' => 'callback',
                'file' => 'AdminPages',
                'rules' => array('Настройка сайта'),
                'callback' => 'AdminPages::UrlAliasDelete',
            ),
            'admin/modules' => array(
                'title' => 'Модули',
                'file' => 'AdminPages',
                'rules' => array('Настройка сайта'),
                'callback' => 'AdminPages::Modules',
            ),
            'admin/modules/toggle' => array(
                'type' => 'callback',
                'file' => 'AdminPages',
                'rules' => array('Настройка сайта'),
                'callback' => 'AdminPages::ModulesToggle',
            ),
        );
    }

    public function Theme() {
        $AdminTheme = Module::GetPath('admin') . '/AdminTheme.class.php';
        return array(
            'dump' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Dump',
                'file' => $AdminTheme,
            ),
            'link' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Link',
                'file' => $AdminTheme,
            ),
            'link-confirm' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::LinkConfirm',
                'file' => $AdminTheme,
            ),
            'link-ajax' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::LinkAjax',
                'file' => $AdminTheme,
            ),
            'menu' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Menu',
                'file' => $AdminTheme,
            ),
            'table' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Table',
                'file' => $AdminTheme,
            ),
            'date' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Date',
                'file' => $AdminTheme,
            ),
            'form' => array(
                'type' => 'callback',
                'callback' => 'AdminTheme::Form',
                'file' => $AdminTheme,
            ),
            'input' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'input.tpl.php',
                'arguments' => array('type' => NULL, 'name' => NULL, 'label' => NULL, 'value' => NULL, 'attributes' => array()),
            ),
            'autocomplete' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'autocomplete.tpl.php',
                'arguments' => array('name' => NULL, 'label' => NULL, 'callback' => NULL, 'value' => NULL, 'attributes' => array()),
            ),
            'form-actions' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'form-actions.tpl.php',
                'arguments' => array('buttons' => array()),
            ),
            'select' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'select.tpl.php',
                'arguments' => array('name' => NULL, 'label' => NULL, 'options' => array(), 'value' => NULL, 'attributes' => array()),
            ),
            'radio' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'radio.tpl.php',
                'arguments' => array('name' => NULL, 'label' => NULL, 'options' => array(), 'value' => NULL, 'attributes' => array()),
            ),
            'block' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'block.tpl.php',
                'arguments' => array('block' => NULL),
            ),
            'control-links' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'control-links.tpl.php',
                'arguments' => array('aLinks' => array()),
            ),
            'checkbox' => array(
                'type' => 'template',
                'template' => Module::GetPath('admin') . DS . 'theme' . DS . 'checkbox.tpl.php',
                'arguments' => array('name' => NULL, 'options' => array(), 'values' => array(), 'attributes' => array()),
            )
        );
    }

    public static function BlockInfo() {
        return array(
            'block-side-menu' => array(
                'title' => (Path::Arg(0) == 'admin') && (Path::Arg(1) == 'block') ? 'Боковая панель' : '',
                'content' => Admin::SideMenuBlock(),
            ),
        );
    }

    public static function SideMenuBlock() {
        if (!User::Access(array('Настройка сайта')) || $_GET['minimal'])
            return;
        global $side_menu;
        $item = '';
        foreach ($side_menu as $module => $aLinks) {
            $module_info = Module::GetInfo($module);
            $title = $module_info ? $module_info['name'] : $module;
            $item .= '<li><div class="sm-module collapsed">' . $title . '</div>';
            $subitem = '';
            foreach ($aLinks as $aLinkInfo) {
                $subitem .= '<li>' . Theme::Render('link', $aLinkInfo['path'], $aLinkInfo['title']) . '</li>';
            }
            $item .= '<ul class="side-menu-sub">' . $subitem . '</ul></li>';
        }
        Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'side-menu.js');
        Theme::AddCss(Module::GetPath('admin') . DS . 'css' . DS . 'side-menu.css');
        return '<div id="side-menu"><div class="sm-head">Администрирование</div><div class="sm-body"><ul class="side-menu-item">' . $item . '</ul></div></div>';
    }

    public static function GetThemes() {
        $aThemes = array();
        $theme_path = 'themes/';
        foreach (glob($theme_path . '*') as $theme)
            if (is_dir($theme)) {
                $theme = str_replace($theme_path, '', $theme);
                $aThemes[$theme] = $theme;
            }
        return $aThemes;
    }

    public static function ModuleInfo($module) {
        $file = CUSTOM_PATH . $module . DS . 'module.info.php';
        if (file_exists($file))
            return include $file;
        return array();
    }

    public static function RuntimeInfo() {
        if (!User::Access('Просмотр статистики выполнения'))
            return;
        global $memory_start;
        $memory = memory_get_usage();
        $memory_total = round((($memory - $memory_start) / 1024) / 1024,2);
        $out .= '<div class="rinfo-row"><b>Использовано памяти:</b> '.$memory_total.' Mb</div>';
        $out .= '<div class="rinfo-row">queries: ' . $GLOBALS['query_counter'] . '</div>';
        $out .= '<div>' . implode("<br>", $GLOBALS['query_list']) . '</div>';
        return $out;
    }

    public static function EventCacheDelete() {
        global $pdo;
        $pdo->query('TRUNCATE forms;');
    }

}