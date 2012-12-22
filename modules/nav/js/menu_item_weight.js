$(function(){
	
	$('#save_weight').on('click',function(){
		var table = $(this).parent().prev();
		var weight = {};
		table.find('tbody tr').each(function(){
			var id = $(this).find('td:eq(0)').text();
			weight[id] = $(this).find('td:last input').val();
		});
		$.post('/admin/menu/menu/save_weight',{'weight':JSON.stringify(weight)},function(data){
			var data = JSON.parse(data);
			CMS.ui.modal(data.message);
			setTimeout(function(){
				window.location.reload();
			},1000);
		});
		return false;
	});
});