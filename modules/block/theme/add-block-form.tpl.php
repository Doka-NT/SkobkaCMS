<div><?=Theme::Render('input','text','title','Заголовок');?></div>
<div><?=Theme::Render('input','textarea','content','Содержимое','',array('editor'=>'true'));?></div>
<?=Theme::Render('form-actions',array(
	'submit'=>array('text'=>'Добавить'),
));?>