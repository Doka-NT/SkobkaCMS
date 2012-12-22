<?/*<?=Theme::Render('autocomplete','parent','Поместить в блог','ajax/blog/autocomplete');*/?>
<?=Theme::Render('input','text','title','Название блога',$blog->title);?>
<?=Theme::Render('input','text','alias','ЧПУ блога',Path::GetAlias($blog->path));?>
<?=Theme::Render('input','textarea','description','Описание',$blog->description);?>
<?=Theme::Render('radio','open','Тип блога',array(
    0=>'Закрытый блог',
    1=>'Открытый блог'
),$blog->id?$blog->open:1);?>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>$blog->id?'Сохранить':'Создать'),
));?>