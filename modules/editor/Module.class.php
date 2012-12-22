<?php

class Editor {

    public function Menu() {
        return array(
            'ajax/admin/editor/php' => array(
                'type' => 'callback',
                'callback' => 'Editor::EditorPhpPage',
                'rules' => array('Использовать php редактор'),
            ),
        );
    }

    public function Rules(){
        return array('Использовать php редактор');
    }
    
    public static function Load($id = 'content_content') {
        $editor = Variable::Get('site_editor', 'tinymce');
        if($editor == 'codemirror'){
            self::LoadCode($id);
            return;
        }
        Core::LoadModule($editor);
        $editor::Load();
        Filemanager::Load();
    }

    public static function LoadCode($id = 'content_content',$mode = 'html',$var_suffix = '') {
        Theme::AddJs(Module::GetPath('editor') . DS . 'codemirror/lib/codemirror.js');
        Theme::AddCss(Module::GetPath('editor') . DS . 'codemirror/lib/codemirror.css');
        switch($mode){
            case 'sql':
            case 'html':
                $js_mode = 'mysql';
                break;
            case 'php':
                $js_mode = 'application/x-httpd-php';
                break;
                
        }
        $types = array(
            'php','javascript','mysql','htmlmixed','xml','clike'
        );
        foreach($types as $type)
            Theme::AddJs(Module::GetPath('editor') . DS . 'codemirror/mode/'.$type . DS . $type . '.js' );
        
return '
<script type="text/javascript">
      editor'.$var_suffix.'_init = function(){ return CodeMirror.fromTextArea(document.getElementById("'.$id.'"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "'.$js_mode.'",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
      });}
      var editor'.$var_suffix.' = editor'.$var_suffix.'_init();
</script>';
    }

    public static function EditorPhpPage() {
        $str = Theme::Template(Module::GetPath('editor') . '/index.html;', array());
        exit($str);
    }

}