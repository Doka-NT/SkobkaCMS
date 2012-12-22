<fieldset class="qb-database">
    <legend>Конструктор</legend>
    <div class="alert">
        <a href="#" id="qb-phaceholder-toggle">Доступны предопределенные placeholder-ы:</a>
        <ul id="qb-placeholder-list">
        <?foreach(QBMisc::PlaceholderInfo() as $ph=>$info):?>
            <li><b><a href="#" class="qb-placeholder" title="Щелкните для подстановки placeholder-а в условие"><?=$ph;?></a></b> - <?=$info;?></li>
        <?endforeach;?>
        </ul>
        <p>
            <b>Пример:</b><em> AND mytable.myfield = :last</em>
        </p>
    </div>
    <fieldset class="col_25">
        <legend>Информация</legend>
        <?=Theme::Render('input','text','name','Название',$query->name);?>
        <?=Theme::Render('input','text','path','Путь',$query->path);?>
        <?=Theme::Render('radio','block','Создать блок',array("Нет","Да"),$query->block);?>
        <br>
        <?=Theme::Render('input','textarea','rules','Правила доступа',$query->rules?implode("\n",$query->rules):'Обычный доступ');?>
    </fieldset>
    <fieldset class="col_25">
        <legend>Таблицы</legend>
        <div class="tables-list">
        <?=QBMisc::Tables();?>
        </div>
    </fieldset>
    <fieldset class="col_25">
        <legend>Поля</legend>
        <div class="field-list">
            <select name="field[]" id="qb-fields" multiple="multiple">

            </select>
        </div>
    </fieldset>
    <fieldset class="col_25">
        <legend>Условия</legend>
        <div class="qb-clause-list">
            <div class="input-append">
                <select name="clause_fields" id="qb-clause-fields" class="span2"></select>
                <input type="button" class="btn" id="qb-add-clause" value="+"/>
            </div>
            <textarea name="clause" id="qb-clause"></textarea>
        </div>
    </fieldset>
    <fieldset class="joins-list-wrapper">
        <legend>Связи</legend>
        <div id="qb-join-list">Связи не требуются</div>
        <div id="qb-join-ready" class="hide"></div>
    </fieldset>
</fieldset>
<fieldset>
    <legend>Препросмотр запроса</legend>
    <textarea name="query" id="query"><?=$query->query?$query->query:'Не достаточно данных';?></textarea>
    <?=Editor::LoadCode('query','sql');?>
</fieldset>
<fieldset>
    <legend>Форматирование</legend>
    <textarea name="template" id="template"><?=$query->template?$query->template:QBMisc::DisplayTemplate();?></textarea>
    <?=Editor::LoadCode('template','php','_template');?>
</fieldset>
<div class="form-actions">
    <input type="submit" value="Сохранить" id="qb-submit" class="btn btn-primary"/>
</div>