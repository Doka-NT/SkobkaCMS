CMS.ui.attach.blog_rating_widget = function(){
    var vote = function(obj,vote){
        var widget = $(obj).parents('.blog-post-rating-widget');
        var post_id = widget.attr('data-post-id');
        CMS.ajax.post(CMS.settings.blog.vote_callback,{post_id:post_id,vote:vote},function(data){
            var data = JSON.parse(data);
            if(data.error)
                CMS.ui.modal(data.error);
            else
                widget.find('.blog-post-rate').text(data.rate);
        });        
    }
    $('span.blog-post-vote-up').on('click',function(){
        vote(this,1);
    });
    $('span.blog-post-vote-down').on('click',function(){
        vote(this,0);
    });
    
}