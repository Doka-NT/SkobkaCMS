<?php

class DBPDO {

    public $database;

    public function __construct() {
        include 'configuration.php';
        try {
            $this->database = new PDO("mysql:host={$DB['host']};dbname={$DB['dbname']};charset=utf8", $DB['user'], $DB['password']);
        } catch (PDOException $e) {
            Notice::Error($e->getMessage());
        }
    }

    public function Get() {
        return $this->database;
    }

    public function Query($sql, $args = array()) {
        if (!$this->database)
            return false;
        if(!$args)
            $args = array();
        if(!is_array($args))
            $args = array($args);        
        $state = $this->database->prepare($sql);
        $state->execute($args);
        $error = $state->errorInfo();
        if ($error[2])
            Notice::Error($error[2]);
        $opt = array('sql' => $sql, 'args' => $args);
        Event::Call('PdoQuery', $opt);
        return $state;
    }

    public function QueryLimit($sql,$args,$limit_from = 0, $limit_till = 1000) {
        return $this->Query($sql . " LIMIT $limit_from , $limit_till ",$args );
    }

    public function PagerQuery($sql,$args,$per_page){
        $page = $_GET['page']?$_GET['page']:1;
        return $this->QueryLimit($sql,$args,($page - 1) * $per_page,$per_page);
    }
    
    public function fetch_object($state) {
        if (!$state)
            return false;
        $state->setFetchMode(PDO::FETCH_OBJ);
        return $state->fetch();
    }

    public function serialize($val) {
        return base64_encode(serialize($val));
    }

    public function unserialize($val) {
        return unserialize(base64_decode($val));
    }

    public function insert($table, array $aData) {
        $aCols = $aNewData = array();
        foreach ($aData as $col => $value)
            if ($value !== null) {
                $aCols[] = $col;
                $aValues[] = ':' . $col;
                $aNewData[$col] = $value;
            }
        return $this->Query('INSERT INTO ' . $table . ' (`' . implode('`,`', $aCols) . '`) VALUES (' . implode(",", $aValues) . ')', $aNewData);
    }

    public function lastInsertId() {
        return $this->database->lastInsertId();
    }

}