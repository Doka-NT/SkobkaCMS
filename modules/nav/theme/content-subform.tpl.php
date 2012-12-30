<div class="accordion" id="content-subform-menu">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#content-subform-menu" href="#content-subform-menu-settings">
                <i class="icon-folder-open"></i> Настройка пункта меню
            </a>
        </div>
        <div id="content-subform-menu-settings" class="accordion-body <?=$item_id?'in':'';?> collapse">
            <div class="accordion-inner">
                <?=Theme::Render('input','text','menu[name]','Название пункта меню',$name);?>
                <label for="menu[parent]">Поместить пункт в:</label>
                <select name="menu[parent]" class="form-select">
                    <?=Nav::GetMenuOptions($parent,$menu_id,$item_id);?>
                </select>
                <?=Theme::Render('input','text','menu[weight]','Вес',$weight);?>
                <input type="hidden" name="menu[menu_item_id]" value="<?=$item_id;?>"/>
            </div>
        </div>
    </div>
</div>