$(function(){
	$('.subcontent-parent').live('change',function(){
		var html = $(this).parent().html();
		$(this).parent().after(html);
		//autocomplete_init();
		CMS.ui.reattach();
	});
	$('.sc-parent-remove').click(function(){
		$(this).parent().slideUp(function(){
			$(this).remove();
		});
		return false;
	});
});