<div><?=Theme::Render('input','text','title','Заголовок',$block->title);?></div>
<div><?=Theme::Render('input','textarea','content','Содержимое',$block->content,array('editor'=>true));?></div>
<input type="hidden" name="block_id" value="<?=$block->bcid;?>" />
<?=Theme::Render('form-actions',array(
	'submit'=>array('text'=>'Сохранить'),
));?>