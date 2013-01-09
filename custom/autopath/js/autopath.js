$(function(){
    $('input:checkbox.autoalias-checkbox').change(function(){
        check_autopath(this);
    });
    
    check_autopath = function(el){
        var prev = $(el).parent().prev();
        if (!$(el).attr('checked'))
            prev.attr('disabled',false);
        else
            prev.attr('disabled',true);        
    };
    
    check_autopath($('input:checkbox.autoalias-checkbox'));
});