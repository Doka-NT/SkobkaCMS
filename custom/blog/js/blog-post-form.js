tinyMCE.init({
    mode : "textareas",
    document_base_url:  '/',
    language:	'ru',
    content_css : CMS.settings.web_root + "static/styles.css",
    setup : function(ed) {
        ed.onKeyPress.add(onChangeCallback);
        ed.onPaste.add(onChangeCallback);
        ed.onChange.add(onChangeCallback);
	    ed.addButton('sk_code', {
            title : 'Вставить код',
            image : 'http://www.rodim.ru/conference/html/UBBC/code.gif',
            onclick : function() {
                ed.focus();
				var text = 'some code';
				ed.setContent(ed.getContent() + '<pre>'+text+'</pre>');
            }
        });
    },        
    theme: 'advanced',
    theme_advanced_buttons1: 'formatselect,fontselect,fontsizeselect|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,image,|,code,removeformat,cleanup,pagebreak,|,sk_code',
    plugins: 'pagebreak,autolink,spellchecker,style,inlinepopups',
    theme_advanced_resizing : true,
    convert_urls : false,
	valid_styles : 'color,font-size,font-weight,font-style,text-decoration',
/*valid_elements : ""
+"a[href|target],"
+"b,"
+"strong,"
+"em,"
+"br,"
+"font[color|face|size],"
+"img[src|id|width|height|align|hspace|vspace],"
+"i,"
+"li,"
+"p[align|class],"
+"h1,"
+"h2,"
+"h3,"
+"h4,"
+"h5,"
+"h6,"
+"span[class],"
+"textformat[blockindent|indent|leading|leftmargin|rightmargin|tabstops],"
+"u"
+"<!--pagebreak-->",*/
    cleanup : true
//onchange_callback : "onChangeCallback"
});