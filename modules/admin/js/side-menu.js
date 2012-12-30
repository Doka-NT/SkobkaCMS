$(function(){
    $('#block-side-menu .block-title').unbind('click').click(function(){
        var sm = $('#side-menu');
        if(sm.hasClass('smhide')){
            sm.animate({
                left:0
            }).removeClass('smhide');
            $('body').css({
                marginLeft:250
            });
        }
        else {
            sm.animate({
                left:'-250'
            }).addClass('smhide');
            $('body').css({
                marginLeft:0
            });
        }
    });
	
    $('.sm-module').click(function(){
        var that = $(this);
        var next = $(this).next();
        $('#block-side-menu .side-menu-sub').not(next).slideUp('fast');
        next.slideToggle(200,function(){
            that.toggleClass('collapsed');
        });
    });

    if($.cookie('side-menu-open') != 1)
        $('#side-menu').css({
            marginLeft:-250
        }).addClass('collapsed');
    $('.sm-head').unbind().click(function(){
        if(!$(this).parent().hasClass('collapsed')){
            $(this).parent().animate({
                marginLeft:-250
            }).addClass('collapsed');
            $.cookie('side-menu-open',0,{
                path: CMS.settings.web_root
            });
        }
        else	{
            $(this).parent().animate({
                marginLeft:0
            }).removeClass('collapsed');
            $.cookie('side-menu-open',1,{
                path: CMS.settings.web_root
            });
        }
    });
});