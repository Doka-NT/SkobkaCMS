<?=Theme::Render('input','text','site_name','Название сайта',Variable::Get('site_name'));?>
<?=Theme::Render('input','text','site_frontpage','Путь главной страницы',Variable::Get('site_frontpage'));?>
<?=Theme::Render('select','site_theme','Тема оформления сайта',Admin::GetThemes(),Variable::Get('site_theme'));?>
<?=Theme::Render('select','site_minimal_theme','Минималистичная тема оформления сайта',Admin::GetThemes(),Variable::Get('site_minimal_theme'));?>
<?=Theme::Render('select','site_editor','Визуальный редактор',array('elrte'=>'ElRTE','tinymce'=>'TinyMCE','codemirror'=>'CodeMirror'),Variable::Get('site_editor', 'tinymce'));?>
<?=Theme::Render('input','text','mail_from','Отправлять почту от адреса',Variable::Get('mail_from','admin@' . $_SERVER['SERVER_NAME']));?>
<?=Theme::Render('input','textarea','mail_template','Шаблон письма',Variable::Get('mail_template','<?=$body;?>'));?>
<?=Editor::LoadCode('mail_template');?>
<?=Theme::Render('radio','compress_files','Объединять css и js файлы',array(0=>'Нет',1=>'Да'),Variable::Get('compress_files',0));?>