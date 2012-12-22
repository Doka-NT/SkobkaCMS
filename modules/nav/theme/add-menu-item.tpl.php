<div class="form-actions">
	<?=Theme::Render('input','text','title','','',array('placeholder'=>'Название'));?>
	<?=Theme::Render('input','text','path','','',array('placeholder'=>'Путь'));?>
	
	<?=Theme::Render('input','text','parent','','',array('placeholder'=>'ID родителя'));?>
	
	<?=Theme::Render('input','submit','addmenuitem','','Добавить',array('class'=>'btn btn-primary'));?>
	<input type="hidden" name="menu_id" value="<?=$menu_id;?>" />
</div>