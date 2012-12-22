<?
foreach($group->rules as $rule)
    $default_value[$rule] = $rule;
?>
<p>Отметьте галочками только те правила которые должны применятся к данной группе.</p>
<? foreach (User::GetAllRules() as $module=>$aRules): ?>
    <fieldset class="rule-fieldset">
        <?        $name = Module::GetInfo($module,true)->name; ?>
        <legend><?=$name?$name:$module;?></legend>
        <?
        $opt = array();
        foreach($aRules as $v)
            $opt[$v] = $v;
        ?>
        <?=Theme::Render('checkbox','rules['.$group->gid.']',$opt,$default_value);?>
    </fieldset>
<? endforeach; ?>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>'Сохранить настройки правил'),
));?>
        