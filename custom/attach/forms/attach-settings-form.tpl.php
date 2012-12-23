<?/*<?=Theme::Render('input','text','attach_picture_dir','Папка для хранения изображений',  Variable::Get('attach_picture_dir','userpic'));?>
<div>Необходимо указать отностительный путь внутри папки <b>files/</b> без начального и конечного слешей.</div>*/?>
<?=Theme::Render('input','text','attach_picture_ext','Допустимые типы файлов', Variable::Get('attach_picture_ext','jpg jpeg png gif doc zip'));?>
<?=Theme::Render('input','text','attach_picture_size','Максимальный размер файла, кб', Variable::Get('attach_picture_size',5 * 1024 ));?>
<?=Theme::Render('select','attach_types[]','Типы материалов, доступных для прикрепления файлов',array(0=>'Ни один') + Content::GetTypes(),Variable::Get('attach_types',array()),array('multiple'=>'multiple','style'=>'height:100px'));?>