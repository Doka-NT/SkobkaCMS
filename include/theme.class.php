<?php

class Theme {

    public $aThemeRegister;

    private static function ThemeDefault() {
        return array(
            'type' => 'callback', // callback, template,
            'template' => null,
            'arguments' => array(),
        );
    }

    public function __construct() {
        global $oEngine;
        $aThemeRegister = array();
        foreach ($oEngine->modules as $oModule)
            if (method_exists($oModule, 'Theme'))
                $aThemeRegister = $oModule->Theme() + $aThemeRegister;
        $this->aThemeRegister = $aThemeRegister;
        $GLOBALS['module_css'] = $GLOBALS['module_js'] = array();
        $GLOBALS['compress'] = Variable::Get('compress_files', false);
    }

    public function Stack() {
        return $this->aThemeRegister;
    }

    public static function Page($sContent) {
        global $theme, $theme_info, $page_title,$web_root;
        $sTpl = Theme::GetThemePath($theme) . DS . 'templates' . DS . 'page.tpl.php';

        $aBlocks = Block::GetList();
        foreach ($theme_info['positions'] as $position) {
            $aVars[$position] = Block::GetByPosition($position, $aBlocks);
            if ((Path::Get() == 'admin/block') && (User::Access('Управление блоками')))
                $aVars[$position] = '<span class="region-view-wrapper"><span class="region-view-name">' . $position . '</span>' . $aVars[$position] . '</span>';
        }

        $aVars += array(
            'content' => $sContent,
            'css' => Theme::GetCss(),
            'js' => Theme::GetJs() . Theme::GetJsSettings(),
            'head_title' => Theme::GetTitle(),
            'title' => $page_title,
            'head' => Theme::GetHead(),
            'messages' => Notice::GetAll(),
            'is_front' => Path::Get() == 'frontpage' ? true : false,
            'theme_path' => Path::Url(Theme::GetPath($theme)),
            'site_name' => Variable::Get('site_name',''),
            'frontpage' => $web_root,
            'runtime_info'=>Admin::RuntimeInfo(),
        );
        return Theme::_Include($sTpl, $aVars);
    }

    private static function _Include($sTpl, $aVars) {
        ob_start();
        extract($aVars, EXTR_OVERWRITE);
        unset($aVars);
        include $sTpl;
        $text = ob_get_contents();
        ob_end_clean();
        return $text;
    }

    /* Clone private function _Include. Must be changed soon */

    public static function Template($sTpl, $aVars, $tpl_prefix = '') {
        global $theme;
        $aTpl = explode(DS, $sTpl);
        $sFile = end($aTpl);
        if (file_exists($sThemeTpl = Theme::GetThemePath($theme) . DS . 'templates' . DS . $tpl_prefix . '-' . $sFile))
            return self::_Include($sThemeTpl, $aVars);
        if (file_exists($sThemeTpl = Theme::GetThemePath($theme) . DS . 'templates' . DS . $sFile))
            return self::_Include($sThemeTpl, $aVars);
        return self::_Include($sTpl, $aVars);
    }

    public static function GetThemePath($sTheme) {
        return 'themes' . DS . $sTheme;
    }

    public static function GetPath($sTheme) {
        return Theme::GetThemePath($sTheme);
    }

    public static function ThemeInfo($sTheme = false) {
        if (!$sTheme)
            $sTheme = $GLOBALS['theme'];
        include_once Theme::GetThemePath($sTheme) . DS . 'theme.class.php';
        $info = new ThemeInfo();
        return $info->Info();
    }

    public static function GetCss() {
        global $theme, $theme_info, $web_root, $module_css, $compress;
        if ($compress)
            return Theme::PackCss();
        $out = '';
        $module_css = array_unique($module_css);
        foreach ($module_css as $sFile) {
            if (!preg_match('/^http(.*)/', $sFile))
                $sFile = $web_root . $sFile;
            $out .= '<link rel="stylesheet" type="text/css" href="' . $sFile . '" />';
        }
        if ($theme_info['css'])
            foreach ($theme_info['css'] as $sFile)
                $out .= '<link rel="stylesheet" type="text/css" href="' . $web_root . Theme::GetThemePath($theme) . DS . $sFile . '" />';
        return $out;
    }

    public static function GetJs() {
        global $theme, $theme_info, $web_root, $module_js, $compress;
        $out = '';
        $module_js = array_unique($module_js);
        foreach ($module_js as $sFile) {
            if (!preg_match('/^http(.*)/', $sFile))
                $sFile = $web_root . $sFile;
            $out .= '<script type="text/javascript" src="' . $sFile . '"></script>';
        }
        /* if ($compress)
          return $out . Theme::PackJs(); */
        if ($theme_info['js'])
            foreach ($theme_info['js'] as $sFile)
                $out .= '<script type="text/javascript" src="' . $web_root . Theme::GetThemePath($theme) . DS . $sFile . '"></script>';
        return $out;
    }

    public static function GetTitle() {
        global $site_name, $page_title;
        return $page_title . ' | ' . $site_name;
    }

    public static function SiteName($site_name) {
        $GLOBALS['site_name'] = $site_name;
    }

    public static function SetTitle($title) {
        $GLOBALS['page_title'] = $title;
    }

    public static function GetHead() {
        global $web_root;
        $additional_meta = '';
        Event::Call('LoadHeadInfo', $additional_meta);
        return '<meta http-equiv="Content-type" content="text/html;charset=utf-8" /><link href="' . $web_root . 'favicon.ico" rel="shortcut icon" type="image/x-icon"/>' . $additional_meta;
    }

    public static function Render($sThemeName) {
        global $oEngine;
        $args = func_get_args();
        array_shift($args);
        if (!$oEngine->theme_stack[$sThemeName])
            return;
        $theme = $oEngine->theme_stack[$sThemeName] + self::ThemeDefault();
        if ($theme['type'] == 'callback') {
            if ($theme['file'])
                include_once $theme['file'];
            return call_user_func_array($theme['callback'], $args);
        }
        if ($theme['type'] == 'template') {
            $_args = array();
            $i = 0;
            foreach ($theme['arguments'] as $k => $v) {
                $_args[$k] = $args[$i] ? $args[$i] : $v;
                $i++;
            }
            $aExplodeFilePath = explode('/', $theme['template']);
            $sTplName = end($aExplodeFilePath);
            if (file_exists($sTplNewFile = Theme::GetThemePath($GLOBALS['theme']) . DS . 'templates' . DS . $sTplName))
                return self::_Include($sTplNewFile, $_args);
            else
                return self::_Include($theme['template'], $_args);
        }
    }

    public static function AddCss($sFile) {
        $GLOBALS['module_css'][] = $sFile;
    }

    public static function AddJs($sFile) {
        $GLOBALS['module_js'][] = $sFile;
    }

    public static function Attr($attr) {
        $attr_str = '';
        foreach ($attr as $attr_name => $attr_value)
            $attr_str .= " {$attr_name}=\"{$attr_value}\"";
        return $attr_str;
    }

    public static function GetPositions() {
        global $theme_info;
        return $theme_info['positions'];
    }

    /* PACK JS AND CSS FILES */

    public static function CompressCss($buffer) {
        Module::IncludeFile('admin', 'CssMin.class.php');
        $oMin = new Minify_YUI_CssCompressor();
        return $oMin->compress($buffer);
    }

    /* PACK CSS */

    public static function PackCss() {
        global $theme, $theme_info, $web_root, $module_css, $compress;
        $out_file_path = STATIC_DIR . DS . 'css' . DS;
        $staticFileName = '';
        $module_css = array_unique($module_css);
        $theme_css = $theme_info['css']?$theme_info['css']:array();
        
        $staticFileName = implode("",$module_css) . implode("", $theme_css);
        $staticFileName = $out_file_path . 'style-' . md5($staticFileName) . '.css';
        if (!File::Exists($staticFileName)) {
            foreach ($module_css as $sFile) {
                $out .= self::_packCssPrepare($sFile);
            }
            
            foreach ($theme_css as $sFile) {
                $out .= self::_packCssPrepare($sFile, false);
            }

            $compressed = Theme::CompressCss($out);
            File::CreateDir($out_file_path);

            file_put_contents($staticFileName, $compressed);
        }
        Theme::AddJsSettings(array(
            'stylesheet'=>$web_root . $staticFileName,
        ));
        return '<link rel="stylesheet" type="text/css" href="' . $web_root . $staticFileName . '" />';
    }

    public static function _packCssPrepare($sFile, $is_module = true) {
        global $theme,$web_root;
        $cssFile = $is_module?file_get_contents($sFile):file_get_contents($sFile = Theme::GetThemePath($theme) . DS . $sFile);
        $info = pathinfo($sFile);
        //$cssFile = str_replace('url(..','url('.$web_root.$info['dirname'],$cssFile);
        $cssFile = str_replace('url(../', 'url(' . $web_root . $info['dirname'] . '/../', $cssFile);
        $cssFile = str_replace('url("../', 'url("' . $web_root . $info['dirname'] . '/../', $cssFile);
        $cssFile = str_replace('url(\'../', 'url(\'' . $web_root . $info['dirname'] . '/../', $cssFile);
        
        return $cssFile;
    }

    /* PACK JS */

    public static function PackJs() {
        global $theme, $theme_info, $web_root, $module_js, $compress;
        $module_js = array_unique($module_js);
        foreach ($module_js as $sFile) {
            $out .= '(function(){' . file_get_contents($sFile) . "})();\n\n";
        }

        if ($theme_info['js'])
            foreach ($theme_info['js'] as $sFile)
                $out .= '(function(){' . file_get_contents(Theme::GetThemePath($theme) . DS . $sFile) . "})();\n\n";
        $out_file = STATIC_DIR . DS . 'scripts.js';

        //$compressed = Theme::Compress($out);
        file_put_contents($out_file, $out);
        return '<script type="text/javascript" src="' . $web_root . $out_file . '"></script>';
    }

    public static function AddJsSettings($data) {
        $data = (object) $data;
        $GLOBALS['js_settings'][] = "'" . json_encode($data) . "'";
    }

    public static function GetJsSettings() {
        global $js_settings;
        if ($js_settings) {
            foreach ($js_settings as &$str)
                $str = 'JSON.parse(' . $str . ')';
            $js_settings = implode(",", $js_settings);
        }
        return '<script type="text/javascript">
				' . ($js_settings ? 'jQuery.extend(true,CMS.settings,' . $js_settings . ');' : '') . '
				jQuery(function(){CMS.settings.onload()});
			</script>';
    }

    public static function Pager($sql, $args = array(), $limit = 20) {
        global $pdo, $web_root;
        $path = Path::Get();

        $pager_items = 5;
        $half = floor($pager_items / 2);
        $total = $pdo->fetch_object($pdo->query("SELECT COUNT(*) as total FROM ($sql) as count_table", $args))->total;
        $total_pages = ceil($total / $limit);
        if ($total <= $limit)
            return;

        $page = (int) $_GET['page'];
        $page = $page > $total_pages ? $total_pages : $page;
        $page = $page < 1 ? 1 : $page;
        //Правая часть
        $i = 0;
        for ($i = $page + 1; $i <= $page + 1 + $half; $i++)
            if ($i <= $total_pages)
                $right .= '<li><a href="' . $web_root . $path . '?page=' . $i . '">' . $i . '</a></li>';
        if ($i < $total_pages)
            $right .= '<li><a href="' . $web_root . $path . '?page=' . $total_pages . '">→</a></li>';

        //Левая часть
        $aLeft = array();
        $i = 0;
        for ($i = $page - 1; $i >= $page - $half - 1; $i--)
            if ($i >= 1)
                $aLeft[] = '<li><a href="' . $web_root . $path . '?page=' . $i . '">' . $i . '</a></li>';
        if ($i >= 1)
            $left = '<li><a href="' . $web_root . $path . '">←</a></li>';
        $left .= implode("", array_reverse($aLeft));

        $li = $left . '<li class="active"><a href="' . $web_root . $path . '?page=' . $page . '">' . $page . '</a></li>' . $right;
        /* if($page + $half < $total_pages)
          $li .= '<li><a href="'.$web_root. $path.'?page='.$total_pages.'">→</a></li>'; */
        return '<div class="pagination pagination-centered"><ul>' . $li . '</ul></div>';
    }

}