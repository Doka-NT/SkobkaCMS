CMS.ui.attach.autocomplete = function(){
	$('input.autocomplete2').each(function(){
		var url = $(this).attr('data-url');
		if(!$(this).parent().hasClass('autocomplete-wrapper'))
			$(this).wrap('<div class="autocomplete-wrapper">');
		if(!url)
			return;
		var that = $(this);
		$(this).autocomplete2({
			serviceUrl: url,
			onSelect: function(value,data){
				var name = that.attr('name');
				that.removeAttr('name');
				if(!that.parent().find('input:hidden').length)
					that.parent().append('<input type="hidden" name="'+name+'" value="'+data+'" />');
				else
					that.next().val(data);
			}
		});
	});
};