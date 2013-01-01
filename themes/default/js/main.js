var CMS = {
	settings: {
		web_root: '/',
		onload: function(){
			CMS.ui.reattach();
		},
		
		forms : {}
	},
	ui: {
		modal: function(message){
			var html = '<div class="modal fade"><div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3>Внимание</h3></div><div class="modal-body">'+message+'</div></div>';
			return $(html).modal();
		},
		attach: {
			attach_forms:function(){
				if(typeof CMS.settings.forms.ajax_list != 'undefined'){
					for(i in CMS.settings.forms.ajax_list){
						var form = $('#'+CMS.settings.forms.ajax_list[i]);
						var iframe_name = 'iframe_'+form.attr('id');
						$('.forms_iframe').remove();
						$('body').append('<iframe name="'+iframe_name+'" id="'+iframe_name+'" class="forms_iframe" style="position:absolute;left:-10000px;top:-10000px;opacity:0;"></iframe>');
						form.attr('target',iframe_name);
						form.append('<input type="hidden" name="form_ajax_allowed" value="1"/>');
						form.off('submit').on('submit',function(){
							var form_iframe = $('#'+iframe_name);
							form_iframe.load(function(){
								var iframe_data = JSON.parse(form_iframe.contents().find('body').html());
								
								var modal_html = iframe_data.data;
								if(iframe_data.message)
									modal_html += Base64.decode(iframe_data.message);
								if(iframe_data.form_message)
									modal_html += Base64.decode(iframe_data.form_message);

								/*LOAD FROM AGAIN*/
								
								if(iframe_data.result == 'prepend'){
                                                                        var new_form = $(Base64.decode(iframe_data.form));
                                                                        new_form.wrap('<div class="form-wrapper"/>');
                                                                        new_form.prepend(modal_html);
									form.replaceWith(new_form.parent().html());
								}
								else{
									CMS.ui.modal(modal_html);
									form.replaceWith(Base64.decode(iframe_data.form));
								}
								CMS.ui.reattach();
                                                                if(iframe_data.callback)
                                                                    window[iframe_data.callback](iframe_data);
								if(iframe_data.replace)
									setTimeout(function(){
										CMS.path.replace(iframe_data.replace);
									},1500);
							});
						});
					};
				}; 
                                
                                if(typeof CMS.settings.forms.sisyphus != 'undefined'){
                                    for(i in CMS.settings.forms.sisyphus){
                                        if(CMS.settings.forms.sisyphus[i])
                                            $('#'+i).sisyphus({autoRelease:true,timeout:1,excludeFields:$('.autocomplete2')});
                                        else {
                                            $('#'+i).sisyphus().manuallyReleaseData();
                                            $('#'+i).each(function(){
                                                this.reset();
                                            });
                                        }
                                    }
                                }
			}
		},
		reattach: function(){
			for(i in CMS.ui.attach){
				CMS.ui.attach[i]();
			}
		}
	},
	ajax: {
		get: function(path,opt,callback){
			$.get(CMS.settings.web_root + path,opt,function(data){
				if(typeof callback != 'undefined')
					return callback(data);
			});
		},
		
		post: function(path,opt,callback){
			$.post(CMS.settings.web_root + path,opt,function(data){
				if(typeof callback != 'undefined')
					return callback(data);
			});	
		}
	},
	
	path: {
		replace:function(path){
			window.location.href = CMS.settings.web_root + path;
		}
	}
}

$(function(){
	$('.block').hover(function(){
		$(this).find('.block-control').css('position','absolute').slideDown('fast');
	},function(){
		$(this).find('.block-control').hide();
	});
        $('.control-links').parent().hover(function(){
            $(this).find('.control-links').css('position','absolute').slideDown('fast');
        },function(){
            $(this).find('.control-links').hide();
        });
	
	$('a.link-confirm').unbind('click').on('click',function(){
		if(window.confirm('Подтвердите выполнение действия!'))
			return true;
		return false;
	});
	
	$('a.link-ajax').unbind('click').on('click',function(){
		var text = $(this).text();
		var link = $(this).attr('href');
		var that = $(this);
		$(this).text('Подождите...');
		$.get(link,function(data){
			that.text('Готово. Еще раз?');
		});
		return false;
	});
	$('a.module-toggle').on('click',function(){
		$(this).parents('tr:first').toggleClass('success');
	});
});

var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}