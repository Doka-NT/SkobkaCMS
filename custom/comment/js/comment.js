CMS.ui.attach.comment = function(){
    $('.comment').hover(function(){
        $(this).find('>.comment-info>.comment-links').show();
    },function(){
        $(this).find('>.comment-info>.comment-links').hide(); 
    });
    
    $('a.comment-reply-link').off('click').on('click',function(){
        var that = $(this).parent().parent().parent();
        var id = that.attr('id');
        $('.comment .comment-reply:not(#'+id+' .comment-reply)').hide();
        that.find('>.comment-reply').slideToggle();
        return false;
    });
    
    $('.comment textarea').off('keydown').on('keydown',function(e){
        if (e.ctrlKey && e.keyCode == 13) {
            $(this).parents('form').submit();
        }
    });
}
comment_callback = function(data){
    //window.location.href = window.location.href;
    }