CMS.ui.attach.qb_admin = function(){
    var setError = function(el){
        if(!el.parent().hasClass('error'))
            return $(el).wrap('<span class="control-group error"/>');
    };
    
    
    var process_joins = function(){
        var tables = $('.qb-table:checked');
        var joins = $('#qb-join-list');
        var fields = $('#qb-fields');
        var join_text = '';
        if(tables.length < 2){
            joins.html('Связи не требуются');
            return;
        }
        for (i = 1; i < tables.length; i++){
            var line = '<div class="join"><select class="join_1" style="width:200px;"><option>Выбрать</option>' + fields.html() + '</select> = <select class="join_2" style="width:200px;"><option>Выбрать</option>' + fields.html() + '</select></div>';
            join_text += line;
        }
        joins.html(join_text);
    };
    
    $('.join select').live('change',function(){
        if(!$(this).val())
            return;
        prebuild_query();
    });
    
    var prebuild_query = function(){
        var EOL = "\n";
        var display = $('#query');
        var empty_text = 'Не достаточно данных';
        var tables = $('.qb-table:checked');
        var fields = $('#qb-fields');
        var clause = $('#qb-clause');
        
        var joins = $('.join');
        
        var query_tables = [];
        var query_fields = '';
        var query = 'SELECT ';
        if(!tables.length){
            display.text(empty_text);
            return;
        }
        if(!fields.val())
            query_fields = '*';
        else
            query_fields = fields.val().join(", ");
        tables.each(function(i,v){
            query_tables[i] = $(v).attr('data-value');
        });
        if(query_tables.length == 1)
            var tables_list = query_tables[0];
        else {
            var tables_list = query_tables[0];
            for(i = 1; i < query_tables.length; i++) {
                tables_list += EOL + 'LEFT JOIN ' + query_tables[i] + ' ON ';
                
                tables_list += $(joins[i-1]).find('.join_1').val() + ' = ' + $(joins[i-1]).find('.join_2').val();
            }
        }
        query += query_fields + EOL + 'FROM ' + tables_list;
        
        if(clause.val()){
            query += EOL + 'WHERE ' + clause.val();
        }
        //Display query
        display.val(query);
        $(editor.getWrapperElement()).remove();
        editor = editor_init();
    };
    
    $('#qb-phaceholder-toggle').on('click',function(){
       $(this).next().slideToggle();
       return false;
    });
    $('#qb-fields option').draggable();
    $('.qb-table').on('change',function(){
        var tables = [];
        $('.qb-table:checked').each(function(i,v){
           tables[i] = $(this).attr('data-value');
        });
        tables = tables.join(",");
        CMS.ajax.post('ajax/qb-admin/get-fields',{tables:tables},function(data){
            var data = JSON.parse(data);
            $('#qb-fields').html(data.fields).change();
        });
        prebuild_query();
    });
    
    $('#qb-fields').on('change',function(){
        var fields = $(this).val();
        var clause_fields = $('#qb-clause-fields');
        clause_fields.empty();
        $(fields).each(function(i,v){
            var opt = '<option name="'+v+'">'+v+'</option>';
            clause_fields.append(opt);
        });
        process_joins();
        prebuild_query();
    });
    
    $('#qb-add-clause').on('click',function(){
        var clause = $('#qb-clause');
        if($(this).prev().val())
            clause.val( (clause.val() ? clause.val() + "\nAND " : '' ) + $(this).prev().val() + ' = ' );
        prebuild_query()
    });
    
    $('.qb-placeholder').on('click',function(){
        var clause = $('#qb-clause');
        clause.val( clause.val() + $(this).text());
        prebuild_query();
        return false;
    });
    
    $('#qb-clause').on('change',function(){
        prebuild_query();
    });
    
    $('#qb-submit').on('click',function(){
        var bool = true;
        if(!$('#name').val()){
            setError($('#name'));
            bool = false;
        }
        if(!$('#path').val()){
            setError($('#path'));
            bool = false;
        }        
        /*if( (!$('#query').val()) || ($(this).val().substr(0, 6) != 'SELECT') ){
            setError($('#query'));
            bool = false;
        }*/        
        if(!bool)
            CMS.ui.modal("Недостаточно данных для создания.");
        return bool;
    });
}