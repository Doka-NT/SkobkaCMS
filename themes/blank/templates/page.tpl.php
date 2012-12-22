<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<title><?=$head_title;?></title>
	<?=$head;?>
	<?=$css;?>
	<?=$js;?>
</head>

<body>

<div id="wrapper">

	<header id="header"><?=$top;?></header><!-- #header-->

	<section id="middle">

		<div id="container">
			<div id="content"><?=$messages;?><?=$content;?></div><!-- #content-->
		</div><!-- #container-->

		<aside id="sideLeft"><?=$left;?></aside><!-- #sideLeft -->

		<aside id="sideRight"><?=$right;?></aside><!-- #sideRight -->

	</section><!-- #middle-->

	<footer id="footer"><?=$bottom;?></footer><!-- #footer -->

</div><!-- #wrapper -->
<?=$absolute;?>
</body>
</html>