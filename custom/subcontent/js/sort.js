CMS.ui.attach.subcontent_sort = function(){
	$('#sc-get').on('click',function(){
		var parent = $('#subcontent-sort-form input[name="parent"]').val();
		CMS.ajax.get('admin/subcontent/sort_callback/'+parent,{},function(data){
			var data = JSON.parse(data);
			$('#subcontent-sort-area').html(data.data);
			$('#subcontent-list').sortable({
				stop: function(event,ui){			
					ui.item.parent().find('li').each(function(i,v){
						$(v).find('input').val(i);
					});
				},
			});
		});
		return false;
	});
	$('#sc-sort-get').on('change',function(){
		$('#sc-get').click();
	}).keypress(function(e){
		if(e.keyCode == 13){
			$('#sc-get').click();
			return false;
		}
	});
};