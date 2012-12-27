<?php

class Variable {
        /**
         * Осуществляет предварительный сбор переменных в массив-таблицу в памяти для быстрого доступа
         * @global object $pdo
         * @global array $var_table
         * таблица переменных
         */
        public static function _Preload(){
            global $pdo;
            $var_table = array();
            $q = $pdo->query("SELECT * FROM variables");
            while($var = $pdo->fetch_object($q))
                $var_table[$var->name] = $var;
            $GLOBALS['var_table'] = $var_table;
        }
        /**
         * Устанавливает временно-независимую переменную
         * @global object $pdo
         * @param string $name
         * @param mixed $value
         */
	public static function Set($name,$value){
		global $pdo;
		$pdo->query("DELETE FROM variables WHERE name LIKE ?",array($name));
		$pdo->insert('variables',array(
			'name'=>$name,
			'value'=>$pdo->serialize($value),
		));
	}
	/**
         * Получает переменную или возращает $default если он отсутствует
         * @global object $pdo
         * @global array $var_table
         * @param string $name
         * @param mixed $default
         * @return mixed
         */
	public static function Get($name,$default = NULL){
		global $pdo,$var_table;
                if(isset($var_table[$name]))
                    return $pdo->unserialize($var_table[$name]->value);
		$q = $pdo->query("SELECT * FROM variables WHERE name LIKE ?",array($name));
		$return = $pdo->unserialize($pdo->fetch_object($q)->value);
		if(!$return)
			return $default;
		return $return;
	}
	/**
         * Удаляет временно-независимую переменную
         * @global object $pdo
         * @param string $name
         * @return bool результат операции
         */
	public static function Delete($name){
		global $pdo;
		$res = (bool)$pdo->query("DELETE FROM variables WHERE name LIKE ?",array($name));
		return $res;
	}	
}