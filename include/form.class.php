<?php

class Form {

    public static function GetForm($form_builder) {
        global $theme;
        $args = func_get_args();
        array_shift($args);
        $aForm = call_user_func_array($form_builder, $args);
        $aForm['action'] = Path::Url(Path::Get());
        $aForm['args'] = $args;
        if (!$aForm['id'])
            return Notice::Error('Необходимо указать id формы');
        //Trigger new event LoadForm
        $aForm['content'] = array();
        if (!is_array($aForm['validate']))
            $aForm['validate'] = array($aForm['validate']);
        if (!is_array($aForm['submit']))
            $aForm['submit'] = array($aForm['submit']);
        Event::Call('FormLoad', $aForm);
        /* FORM ARGUMENTS */
        $_args = array();
        if (is_array($aForm['arguments'])) {
            $i = 0;
            foreach ($aForm['arguments'] as $argName => $arg) {
                $_args[$argName] = $args[$i] ? $args[$i] : $arg;
                $i++;
            }
        }
        $_args = $_args ? $_args : $args;
        /* FORM BUILDING */
        /* TYPE TEMPLATE */
        if ($aForm['type'] == 'template') {
            $aExplodeFilePath = explode('/', $aForm['template']);
            $sTplName = end($aExplodeFilePath);

            if (file_exists($sTemplateFile = Theme::GetThemePath($theme) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $sTplName))
                $aForm['content'][0] = Theme::Template($sTemplateFile, $_args);
            else
                $aForm['content'][0] = Theme::Template($aForm['template'], $_args);
        }
        /* TYPE CALLBACK */
        elseif ($aForm['type'] == 'callback') {
            $data = call_user_func_array($aForm['callback'], $_args);
            $_data = array('data' => &$data, 'form' => &$aForm);
            Event::Call('FormCallbackDataAlter', $_data);
            if (is_array($data)) {
                $data = implode("", $data);
            }
            $aForm['content'][0] = $data;
        }
        if($aForm['fields']){
            $aForm['content'] = array(implode("\n",$aForm['fields']));
        }
        if ($aForm['standart']) {
            $aForm['content'][9999] = Theme::Render('form-actions', array('submit' => array('text' => 'Сохранить')));
            if(!is_array($aForm['submit']))
                $aForm['submit'] = array();
            $aForm['submit'][] = 'Form::StandartSubmit';
        }

        /* AJAXify form */
        if ($aForm['ajax']) {
            Theme::AddJsSettings(array(
                'forms' => array(
                    'ajax_list' => array(
                        $aForm['id'],
                    ),
                ),
            ));
        }

        //Form::AllowValidate($aForm);
        ksort($aForm['content']);
        Form::AllowSubmit($aForm);
        Form::Process($aForm);

        return Theme::Render('form', $aForm);
    }

    public static function AllowValidate(&$aForm) {
        
    }

    protected static function AllowSubmit(&$aForm) {
        global $pdo;
        $forms_data = $pdo->fetch_object($pdo->query("SELECT * FROM forms WHERE form_id = ?", array($aForm['id'])));
        if ($forms_data) {
            $aForm['hash'] = $forms_data->form_hash;
            $aForm['object'] = $forms_data;
        } else {
            $aForm['hash'] = md5($aForm['id'] . Core::GetSalt());
            $pdo->insert('forms', array(
                'form_id' => $aForm['id'],
                'form_hash' => $aForm['hash'],
                'validate' => $pdo->serialize($aForm['validate']),
                'submit' => $pdo->serialize($aForm['submit']),
            ));
            $aForm['object'] = $pdo->fetch_object($pdo->query("SELECT * FROM forms WHERE form_id = ?", array($aForm['id'])));
        }
        $aForm['content'][] = '<input type="hidden" name="form_hash" value="' . $aForm['hash'] . '" />';
    }

    protected static function Process(&$aForm) {
        if ($hash = $_POST['form_hash'])
            if ($hash == $aForm['object']->form_hash) {
                $aResult = array();
                array_unshift($aForm['args'], $aResult);
                
                $validate_success = TRUE;
                /* Check required fields as filled */
                if ($aForm['required'])
                    foreach ($aForm['required'] as $label => $fieldName) {
                        if (!$_POST[$fieldName]) {
                            if (is_int($label))
                                Notice::Error('Пожалуйста заполните обязательные поля.');
                            else
                                Notice::Error('Пожалуйста заполните поле <b><i>' . $label . '</i></b>.');
                            $validate_success = FALSE;
                        }
                    }
                if ($validate_success) {
                    $aForm['args']['POST'] = $_POST;
                    $aForm['args']['validate_success'] = $validate_success;
                    if (is_array($aForm['validate']))
                        foreach ($aForm['validate'] as $validate_callback)
                            if ($validate_callback)
                                call_user_func_array($validate_callback, array(&$aForm['args'], $aForm));
                    if (!$aForm['args']['validate_success'])
                        return Path::Replace($aForm['action']);
                    if (is_array($aForm['submit']))
                        foreach ($aForm['submit'] as $submit_callback)
                            if ($submit_callback)
                                call_user_func_array($submit_callback, array(&$aForm['args'], $aForm));
                }
                if ($aForm['ajax']) {
                    Core::Json(array(
                        'data' => base64_encode(implode("<br>", (array) $aForm['args']['message'])),
                        'result' => $aForm['result'] ? $aForm['result'] : 'prepend',
                        'replace' => $aForm['args']['replace'],
                        'form' => base64_encode(Theme::Render('form', $aForm)),
                        'form_message' => 0,
                        'callback' => $aForm['args']['js_callback'] ? $aForm['args']['js_callback'] : $aForm['js_callback'],
                    ));
                }

                if ($path = $aForm['args']['replace'])
                    return Path::Replace($path);
                Path::Replace($aForm['action']);
            }
    }

    public static function StandartSubmit(&$aResult) {
        foreach ($_POST as $var => $value)
            if ($var != 'form_hash') {
                Variable::Set($var, $value);
            }
        Notice::Message('Изменения сохранены');
    }

}