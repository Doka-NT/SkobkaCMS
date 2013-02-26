<?php

/**
 * Определяет путь до папки дополнительных модулей
 */
define('CUSTOM_PATH', 'custom/', TRUE);
/**
 * Определяет разделитель директорий.
 */
define('DS', '/', true);
/**
 * Папка для хранения статических файлов, создаваемых движком
 */
define('STATIC_DIR', 'static', true);
/**
 * Текущая версия CMS, требуется для правильного обновления базы
 */
define('CMS_VERSION', 3.0);

/**
 * Основной класс ядра CMS
 */
class Core {

    /**
     * Объект загруженных модулей
     * @var object
     */
    public $modules;

    /**
     * Запуск основной системы CMS
     */
    public function __construct() {
        $GLOBALS['memory_start'] = memory_get_usage();
        $this->magic_quotes_off();
        $this->LoadFiles();

        $GLOBALS['pdo'] = new DBPDO();

        Path::_Preload();
        Variable::_Preload();

        $this->modules = $this->LoadModules();

        $GLOBALS['session'] = User::AuthCookie();
        $this->modules += $this->LoadCustomModules();
        $this->modules = (object) $this->modules;
        //unset($GLOBALS['_ENV'],$GLOBALS['HTTP_ENV_VARS']);
        $GLOBALS['oEngine'] = $this;
        $this->menu = new Menu();
        $GLOBALS['oEngine'] = $this;
        $this->LoadSettings();

        $this->CallInit();
        $theme = new Theme();

        $GLOBALS['oEngine']->theme_stack = $theme->Stack();
        Event::Call('Loaded');
        Event::Call('FullLoaded');
    }

    /**
     * Загрузка основных классов ядра.
     */
    private function LoadFiles() {
        $aFiles = array(
            'event',
            'pdo',
            'module',
            'notice',
            'path',
            'menu',
            'theme',
            'form',
            'variable',
            'session',
            'file',
            'image',
            'date',
	    'update'
        );
        foreach ($aFiles as $sFile)
            require 'include/' . $sFile . '.class.php';
    }

    /**
     * Загружает системные модули ядра.
     * @return $aModules
     */
    private function LoadModules() {
        $aFiles = array(
            'admin', 'user', 'content', 'block', 'nav', 'filemanager', 'editor', 'jqueryui', 'mail',
        );
        $aModules = array();
        foreach ($aFiles as $sFile) {
            require 'modules/' . $sFile . '/Module.class.php';
            $aModules[$sFile] = new $sFile();
        }
        return $aModules;
    }

    /**
     * Устанавливает системные настройки
     */
    private function LoadSettings() {

        $GLOBALS['theme'] = $_GET['minimal'] ? Variable::Get('site_minimal_theme', 'default') : Variable::Get('site_theme', 'default');
        $GLOBALS['theme_info'] = Theme::ThemeInfo();
        $GLOBALS['site_name'] = 'Тестовый сайт';
        $GLOBALS['web_root'] = '/';
        $GLOBALS['root_host'] = 'http://' . $_SERVER['SERVER_NAME'] . $GLOBALS['web_root'];
        //$GLOBALS['session'] = session_name();
        session_start();
    }

    /*
     * Возвращает соль для хеширования
     */

    public static function GetSalt() {
        return $GLOBALS['salt'];
    }

    /**
     * Выключает действие magic_quotes
     */
    private function magic_quotes_off() {
        if (get_magic_quotes_gpc()) {
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        }
    }

    /**
     * Загружает дополнительные модули
     * @global object $pdo
     * глабальные объект интерфейса БД
     * @return array $aModules
     * Массив объектов загруженных модулей
     */
    private static function LoadCustomModules() {
        global $pdo;
        $q = $pdo->query("SELECT * FROM modules WHERE status = 1");
        $aModules = array();
        while ($module = $pdo->fetch_object($q)) {
            $file = CUSTOM_PATH . $module->name . DS . 'module.class.php';
            if (file_exists($file)) {
                include $file;
                $aModules[$module->name] = new $module->name();
            }
        }
        return $aModules;
    }

    /**
     * Вызывает метод Init у всех загруженных модулей
     */
    private function CallInit() {
        foreach ($this->modules as &$module)
            if (method_exists($module, 'Init'))
                $module->Init();
    }

    /**
     * Загружает указанный модуль и вызывает у него Init
     * @param type $module 
     * системное имя модуля
     * @return object $module 
     * объект модуля
     */
    public static function LoadModule($module) {
        $file = Module::GetPath($module) . DS . 'Module.class.php';
        if (!file_exists($file))
            return false;

        include_once $file;
        $oModule = new $module();
        if (method_exists($oModule, 'Init'))
            $oModule->Init();
        return $oModule;
    }

    /**
     * Выводит переданные данные в формате JSON и завершает работу
     * @param type $data
     */
    public static function Json($data) {
        if (!is_array($data))
            $data = array('data' => $data);
        if (!$data['status'])
            $data['status'] = true;
        if (!$data['message'])
            $data['message'] = base64_encode(Notice::GetAll());

        exit(json_encode($data));
    }

    /**
     * Завершает работу с сообщением $message
     * @param string $message
     */
    public static function Off($message = '') {
        exit($message);
    }

    /**
     * Устанавливает HTTP заголовок
     * @param string $name 
     * Имя заголовка
     * @param string $value
     * Значение
     * @param int $http_response_code
     * HTTP код ответа
     */
    public static function Header($name, $value, $http_response_code = false) {
        $header = $name . ': ' . $value;
        if (($name == 'Location') || ($name == 'location'))
            $http_response_code = 301;
        header($header, true, $http_response_code);
    }

    /**
     * Производит обновление в базе данных
     * @global object $pdo
     * @param int $cmsVersion
     * @param int $startUpdateFromVersion
     * @param array $sql
     */
    public static function UpdateDatabase($cmsVersion, $startUpdateFromVersion, array $sql) {
        global $pdo;
        $ActualDbVersion = Variable::Get('ActualDbVersion',100);
	if(($ActualDbVersion - $cmsVersion) != 1)
	    Notice::Message('База данных SkobkaCMS была обновлена с версии <b>'.$ActualDbVersion.'</b> до версии <b>'.$cmsVersion.'</b>');
        for ($currentVersion = 100; $currentVersion <= $cmsVersion; $currentVersion++) {
            if (!array_key_exists($currentVersion, $sql))
                continue;
            if(($currentVersion < $ActualDbVersion) || ($currentVersion < $startUpdateFromVersion))
                continue;
            
            foreach ($sql[$currentVersion] as $query) {
                Notice::Message('<b>Обвновление. Выполнен запрос:</b> ' . $query);
                $pdo->Query($query, array());
            }
        }
    }

}