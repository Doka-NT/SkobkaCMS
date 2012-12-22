<?php
	$attr = array(
		'style'=>'width:25%;margin-right:10px;',
	);
?><h4>Добавить псевдоним</h4>

<?=Theme::Render('input','text','path','Существующий путь','',$attr);?>
<?=Theme::Render('input','text','alias','Псевдоним','',$attr);?>
<?=Theme::Render('form-actions',array(
	'submit'=>array('text'=>'Добавить'),
));?>