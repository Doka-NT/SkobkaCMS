<?=Theme::Render('input','text','profile_picture_dir','Папка для хранения изображений',  Variable::Get('profile_picture_dir','userpic'));?>
<div>Необходимо указать отностительный путь внутри папки <b>files/</b> без начального и конечного слешей.</div>
<?=Theme::Render('input','text','profile_picture_ext','Допустимые типы файлов', Variable::Get('profile_picture_ext','jpg jpeg png gif'));?>
<?=Theme::Render('input','text','profile_picture_size','Максимальный размер файла, кб', Variable::Get('profile_picture_size',2 * 1024 ));?>
<?=Theme::Render('input','text','profile_picture_wh','Размеры изображения профиля',  Variable::Get('profile_picture_wh','300x300'));?>