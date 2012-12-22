$(function(){
	$('input:checkbox.autoalias-checkbox').change(function(){
		var prev = $(this).parent().prev();
		if (prev.attr('disabled'))
			prev.attr('disabled',false);
		else
			prev.attr('disabled',true);
	});
});