<label>Подписаться на следующие рассылки:</label>
<?=Theme::Render('checkbox','list',$aList,array_flip(Notification::GetList($GLOBALS['user']->mail)));?>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>'Сохранить'),
));?>