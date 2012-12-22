<?php
	if(Path::Arg(2) != 'edit'){
		$innerForm = '';	
		Event::Call('ContentAddForm');
		$content_type = Path::Explode();
		$content_type = $content_type[3];
		
		if(!Content::TypeLoad($content_type))
			return Menu::NotFound();
	}elseif(Path::Arg(2) == 'edit'){
		if(!$oContent)
			return Menu::NotFound();
	}
	
?><div>
	<?=Theme::Render('input','text','content_name','Заголовок',$oContent->title);?>
</div>
<div>
	<?=Theme::Render('input','textarea','content_content','Содержание',$oContent->data,array('rows'=>10,'cols'=>40,'editor'=>'true',));?>
</div>
<?if($innerForm):?><?=$innerForm;?><?endif;?>
<div>
	<?=Theme::Render('radio','content_status','Публиковать',array(0=>'Не публиковать','Публиковать'),$oContent?$oContent->status:1);?>
</div>
<?=Theme::Render('form-actions',array(
	'submit'=>array('text'=>'Сохранить'),
));?>
<input type="hidden" name="content_content_type" value="<?=$content_type?$content_type:$oContent->type;?>" />