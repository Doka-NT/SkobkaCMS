<div><?=Theme::Render('input','text','name','Имя пользователя');?></div>
<div><?=Theme::Render('input','password','password','Пароль');?></div>
<div class="user-auth-button"><?=Theme::Render('input','submit','login','','Войти',array('class'=>'btn'));?></div>
<ul>
    <li><?=Theme::Render('link','user/password','Забыли пароль?');?></li>
    <li><?=Theme::Render('link','user/register','Регистрация');?></li>
</ul>