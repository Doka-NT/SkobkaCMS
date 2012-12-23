(function($){
	jQuery.upload5 = function(path, file_input, callback, progress_callback) {
		$.get(path);
		var http = new XMLHttpRequest();
		if (http.upload && http.upload.addEventListener) {
			http.upload.addEventListener('progress',function(e) {
				if (e.lengthComputable) {
					var percent = (e.loaded / e.total ) * 100;
					if(typeof progress_callback != 'undefined')
						progress_callback(percent,e.loaded,e.total,e);
				}
			},false);
	
			http.onreadystatechange = function () {
				if (this.readyState == 4) {
					if(this.status == 200) {
						if(typeof callback != 'undefined')
							callback(this.response,this.status);
					} else {
						
					}
				}
			};
	
			http.upload.addEventListener('load',function(e) {
			
			});
	
			http.upload.addEventListener('error',function(e) {
				if(typeof callback != 'undefined')
					callback(this.response,this.status);	
			});
		}
		
		var form = new FormData(); 
                var input_name = $(file_input).attr('name');
		for (var i = 0; i < file_input.files.length; i++) {
			form.append(input_name + '[]', file_input.files[i]); 
		}
                form.append('input_name',input_name);
		http.open('POST', path); 
		http.send(form); 
	};
})(jQuery);