CMS.ui.attach.block_list = function(){
    $('a.admin-block-settings').click(function(){
        var el = $(this).parent().find('.block-list-settings');
        var cont = '<div class="predialog">'+ el.html() + '</div>';
        $(cont).dialog({
            'title':'Настроить блок',
            'modal':true,
            close : function(){
                el.html($(this).detach());
            },
            buttons: {
                'Сохранить':function(){      
                    $(this).dialog('close');
                }
            }
        });
        return false;
    });
}