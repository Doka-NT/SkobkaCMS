CMS.ui.attach.tinyMCEinit = function(){
tinyMCE.init({
    language:	'ru',
    // General options
    mode : "textareas",
    theme : "advanced",
    plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

    // Theme options
    theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    content_css : CMS.settings.stylesheet,
    file_browser_callback : 'elFinderBrowser',
    document_base_url:  '/',
    convert_urls : false
});
}
function elFinderBrowser (field_name, url, type, win) {
    var elfinder_url = '/admin/files?minimal=true&tinymce=true';    // use an absolute path!
    tinyMCE.activeEditor.windowManager.open({
        file: elfinder_url,
        title: 'elFinder 2.0',
        width: 900,  
        height: 450,
        resizable: 'yes',
        inline: 'yes',    // This parameter only has an effect if you use the inlinepopups plugin!
        popup_css: false, // Disable TinyMCE's default popup CSS
        close_previous: 'no'
    }, {
        window: win,
        input: field_name
    });
    return false;
}