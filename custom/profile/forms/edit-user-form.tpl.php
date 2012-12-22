<?
$account = $arguments['account'];
?>
<div class="profile">
    <label>Фотография</label>
    <div class="profile-picture">
    <? if ($account->profile->picture): ?>
        <?=Theme::Render('profile-picture',$account);?>
    <? endif; ?>
    </div>
    <?if(User::Access('Загружать изображение')):?>
    <div class="upload-area">
        <div class="upload-area-inner">Перетащите сюда файл</div>
        <input type="file" name="profile[picture]" id="profile-picture"/>
    </div>
    <div class="upload-bar">
        <div class="progress">
            <div class="bar"></div>
        </div>
    </div>
    <?endif;?>    
    <div class="profile-personal">
        <label>Персональная информация</label>
        <input type="text" name="profile[surname]" class="input-medium" placeholder="Фамилия" value="<?=$account->profile->surname;?>"/>
        <input type="text" name="profile[name]" class="input-medium" placeholder="Имя" value="<?=$account->profile->name;?>"/>
        <input type="text" name="profile[middlename]" class="input-medium" placeholder="Отчество" value="<?=$account->profile->middlename;?>"/>
    </div>
    <div class="profile-birthday">
        <label>Дата рождения</label>
        <input type="text" class="input-date" name="profile[birthday]" placeholder="Дата рождения" value="<?=date('d.m.Y',$account->profile->birthday);?>"/>
    </div>
    <div class="profile-other">
        <label>Прочее</label>
        <input type="text" name="profile[occupation]" placeholder="Род занятий" value="<?=$account->profile->occupation;?>"/>
        <input type="text" name="profile[homepage]" placeholder="Персональная страница" value="<?=$account->profile->homepage;?>"/>
    </div>
</div>