<?php

class Path {
    
    public static function _Preload(){
        global $pdo;
        $path_table = $alias_table = array();
        $q = $pdo->query("SELECT * FROM url_alias");
        while($rec = $pdo->fetch_object($q)){
            $path_table[$rec->path] = $rec;
            $alias_table[$rec->alias] = $rec;
        }
        $GLOBALS['path_table'] = $path_table;
        $GLOBALS['alias_table'] = $alias_table;
    }
    
    public static function Get() {
        //$path = trim($_SERVER['REQUEST_URI'],'/');
        //$_GET['path']?$_GET['path']:'frontpage';
        //var_dump($_GET['path'],$_SERVER['REQUEST_URI']);
        global $path;
        if(!$path)
            $path = trim(preg_replace('/\?(.*)/','',$_SERVER['REQUEST_URI']),'/');
        $GLOBALS['path'] = $path;
        $path = $path ? $path : 'frontpage';
        if($path == 'frontpage')
            $GLOBALS['is_front'] = TRUE;
        return $path;
            
    }

    public static function GetOrign() {
        return $GLOBALS['query_path'];
    }

//        public static function Rewrite($path){
//            return $_GET['path'] = $path;
//        }

    public static function Explode($path = null) {
        $path = ($path ? $path : Path::Get());
        return explode('/', $path);
    }

    public static function Url($path,$no_web_root = false) {
        global $web_root;
        if (preg_match('/^http(.*)/', $path))
            return $path;
        $path = ltrim($path, '/');
        $path = Path::GetAlias($path);
        if($no_web_root)
            return $path;
        $path = ($path == 'frontpage' ? $web_root : $web_root . $path);
        //TODO: Add getting url alias
        return $path;
    }

    public static function PathMatch($path, $pathToMatch) {
        if (strpos($path, $pathToMatch) === 0)
            return true;
        return false;
    }

    public static function Replace($path) {
        $path = Path::Url($path);
        header('Location:' . $path, true, 301);
        exit;
    }

    public static function Back() {
        Path::Replace($_SERVER['HTTP_REFERER']);
    }

    public static function QArg($arg_no = false) {

        $path = explode('/', $GLOBALS['query_path']);
        if ($arg_no !== false)
            return urldecode($path[$arg_no]);
        return $path;
    }

    public static function Arg($arg_no = false) {
        $path = Path::Explode(Path::Get());
        if ($arg_no !== false)
            return $path[$arg_no];
        return $path;
    }

    public static function GetByAlias($path) {
        global $pdo,$alias_table;
        if($alias_table[$path])
            return $alias_table[$path]->path;
        $rec = $pdo->fetch_object($pdo->query("SELECT * FROM url_alias WHERE alias LIKE ?", array($path)));
        if ($rec->path)
            return $rec->path;
        $aPath = Path::Arg();
        $count = count($aPath);
        array_pop($aPath);
        for ($i = 0; $i < $count; $i++) {
            $rec = $pdo->fetch_object($pdo->query("SELECT * FROM url_alias WHERE alias LIKE ?", array(implode("/", $aPath))));
            if ($rec->path)
                return $rec->path;
            array_pop($aPath);
        }
        return $path;
    }

    public static function GetAlias($path) {
        global $pdo,$path_table;
        return $path_table[$path]->alias?$path_table[$path]->alias:$path;
        $rec = $pdo->fetch_object($pdo->query("SELECT * FROM url_alias WHERE path LIKE ?", array($path)));
        if ($rec->alias)
            return $rec->alias;
        return $path;
    }

    public static function PrepareAlias($alias) {
        global $pdo,$alias_table;
        $res = $alias_table[$alias];
        //$res = $pdo->fetch_object($pdo->query("SELECT * FROM url_alias WHERE alias LIKE ?", array($alias)));
        if ($res)
            $alias = $alias . '-' . date('dmY-Hi');
        return $alias;
    }

    public static function DeleteAlias($path, $is_alias = false) {
        global $pdo,$alias_table,$path_table;
        unset($path_table[$path]);
        unset($alias_table[$path]);
        if (!$is_alias){
            return $pdo->query("DELETE FROM url_alias WHERE path LIKE ?", array($path));
	}
	
        return $pdo->query("DELETE FROM url_alias WHERE alias LIKE ?", array($path));
    }

    public static function AddAlias($path, $alias) {
        global $pdo,$path_table,$alias_table;
        $alias = self::PrepareAlias($alias);
        $pdo->insert('url_alias', $data = array(
            'path' => $path,
            'alias' => $alias,
        ));
        $data['uaid'] = $pdo->lastInsertId();
        $path_table[$path] = (object)$data;
        $alias_table[$alias] = (object)$data;
        return $alias;
    }
    
    public static function IsFront(){
        global $is_front;
        return $is_front?true:false;
    }
}