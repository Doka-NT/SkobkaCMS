<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$title;?></title>
		<?=$head;?>
		<?=$css;?>
		<?=$js;?>
	</head>
	<body>
		<div class="page">
			<?if($sidebar):?><div class="sidebar">
				<?=$sidebar;?>
			</div>
			<?endif;?>
			<div class="page-inner<?=$sidebar?'':' no-sidebar';?>">
				<h1 class="page-title"><?=$title;?></h1>
				<?=$messages;?>
				<?=$content;?>
			</div>
		</div>
		<?=$absolute;?>
	</body>
</html>