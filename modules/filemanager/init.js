$().ready(function() {
	var elf = $('#elfinder').elfinder({
		url : '/ajax/admin/files',  // connector URL (REQUIRED)
		lang: 'ru',             // language (OPTIONAL)
	}).elfinder('instance');
});
