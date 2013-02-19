<?if(User::Access('Управление пользователями')):?>
    <?  
        $opt = array();
        foreach (User::LoadGroup() as $oGroup)
            $opt[$oGroup->gid] = $oGroup->name;
    ?>
    <?=Theme::Render('radio','user[group]','Группа пользовтеля',$opt,$account->gid);?>
    <p>Выберите группу к которой будет принадлежать пользователь</p>
<?endif;?>
<?=Theme::Render('input','text','user[name]','Имя пользователя',$account->name,array('disabled'=>'disabled'));?>
<?=Theme::Render('input','text','user[mail]','Эл.почта',$account->mail);?>
<?=Theme::Render('input','password','user[password]','Новый пароль');?>

<?=Theme::Render('form-actions',array(
    'cancel'=>array('text'=>'Отмена'),
    'submit'=>array('text'=>'Сохранить'),
));?>