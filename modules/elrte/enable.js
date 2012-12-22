$(function(){
	var opts = {
		lang         : 'ru',   // set your language
		styleWithCSS : false,
		height       : 400,
		fmAllow		 : true,
		toolbar      : 'maxi'
    };
    // create editor
    $('textarea#content, textarea#content_content').elrte(opts);
	opts.toolbar = 'tiny';
	$('textarea#comment').elrte(opts);
});