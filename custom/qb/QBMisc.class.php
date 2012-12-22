<?php

class QBMisc {
    public static function Tables(){
        global $pdo;
        $q = $pdo->query("SHOW TABLES");
        $out = '';
        while($table = $pdo->fetch_object($q)){
            $table = (array)$table;
            $table = reset($table);
            $out .= '<div><label><input type="checkbox" name="table['.$table.']" class="qb-table" data-value="'.$table.'"/> '.$table.'</label></div>';
        }
        return $out;
    }
    
    public static function Fields($table){
        global $pdo;
        $fields = array();
        $q = $pdo->query("DESCRIBE {$table}");
        while($field = $pdo->fetch_object($q))
            $fields[] = "<option value=\"{$table}.{$field->Field}\">{$table}.{$field->Field}</option>";
        return implode(PHP_EOL,$fields);
    }
    
    public static function PlaceholderInfo(){
        $ph = array(
            ':arg_0'    =>  'Подставляет первый аргумент адреса, т.е. /<b>some</b>/example/path.',
            ':arg_1'    =>  'Подставляет первый аргумент адреса, т.е. /some/<b>example</b>/path.',
            ':arg_N'    =>  'Подставляет N-ый аргумент адреса.',
            ':last'     =>  'Подставляет последний агрумент адреса.',
            ':time'     =>  'Подставляет результат функции time().',
            ':uid'      =>  'Подставляет uid текущего пользователя.'
        );
        Event::Call('PlaceholderInfo',$ph);
        return $ph;
    }
    
    public static function DisplayTemplate(){
        return file_get_contents(Module::GetPath('QB') . DS . 'templates' . DS . 'qb-display.tpl.php');
    }
}
