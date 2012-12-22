<?php
if(!$direct)
	include '../../configuration.php';
if((FM_SECURITY_KEY == $_GET['skey'])||($direct)):
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>elFinder 2.0</title>

		<!-- jQuery and jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="/modules/filemanager/css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="/modules/filemanager/css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="/modules/filemanager/js/elfinder.min.js"></script>

		<!-- elFinder translation (OPTIONAL) -->
		<script type="text/javascript" src="/modules/filemanager/js/i18n/elfinder.ru.js"></script>

		<!-- elFinder initialization (REQUIRED) -->
		<?if(!$_GET['tinymce']):?>
		<script type="text/javascript" charset="utf-8">
		
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					url : '/ajax/admin/files',  // connector URL (REQUIRED)
					lang: 'ru',             // language (OPTIONAL)
				}).elfinder('instance');
			});
		</script>
		<?else:?>
		<script type="text/javascript" src="/modules/tinymce/tiny_mce/tiny_mce_popup.js"></script>
		<script type="text/javascript" charset="utf-8">
			var FileBrowserDialogue = {
				init: function() {
					// Here goes your code for setting your custom things onLoad.
				},
				mySubmit: function (URL) {
					var win = tinyMCEPopup.getWindowArg('window');
					// pass selected file path to TinyMCE
					win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = URL;
					// are we an image browser?
					if (typeof(win.ImageDialog) != 'undefined') {
						// update image dimensions
						if (win.ImageDialog.getImageData) {
							win.ImageDialog.getImageData();
						}
						// update preview if necessary
						if (win.ImageDialog.showPreviewImage) {
							win.ImageDialog.showPreviewImage(URL);
						}
					}
					// close popup window
					tinyMCEPopup.close();
				}
			}
			tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);		
		
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					url : '/ajax/admin/files',  // connector URL (REQUIRED)
					lang: 'ru',             // language (OPTIONAL)
					getFileCallback: function(url) { // editor callback
						FileBrowserDialogue.mySubmit(url); // pass selected file path to TinyMCE 
					}
				}).elfinder('instance');
			});
		</script>		
		<?endif;?>
		
	</head>
	<body>

		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>
<?endif;?>