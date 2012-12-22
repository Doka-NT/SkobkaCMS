<div class="comment-form-inner">
<?=Theme::Render('input','textarea','comment','Комментарий',$comment?strip_tags($comment->content):'');?>
</div>
<input type="hidden" name="object" value="<?=$object;?>"/>
<input type="hidden" name="parent" value="<?=$parent;?>"/>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>$comment?'Сохранить':'Отправить'),
));?>