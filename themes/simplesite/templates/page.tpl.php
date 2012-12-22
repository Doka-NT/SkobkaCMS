
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
		<title><?=$head_title;?></title>
		<?=$head;?>
		<?=$css;?>
		<?=$js;?>
</head>

<body>
  <div class="container">
  
    <header class="header clearfix">
      <div class="logo"><a href="/"><img src="/themes/simplesite/images/logo.png" alt="Skobka.CMS"/></a></div>
      <nav class="menu_main">
		<?=$top;?>
		<??>
      </nav>
    </header>
    
    <div class="info">
      <article class="hero clearfix" id="about">
        <div class="col_66">
			<?=$top1;?>
        </div>
        
        <div class="col_33" id="skins">
			<?=$top2;?>
        </div>
      </article>
    
      <article class="article clearfix">
        <div class="col_33"><?=$user1;?></div>
        <div class="col_33"><?=$user2;?></div>
        <div class="col_33"><?=$user3;?></div>
        
        <div class="clearfix"></div>
        

        <h1 id="samples"><?=$title;?></h1>
        
        <div class="col_100">
			<?=$messages;?>
			<?=$content;?>
		</div>
		
        <div class="clearfix"></div>
                
      </article>
    </div>
    
    <footer class="footer clearfix">
		<?/*<div><?=Admin::RuntimeInfo();?></div>*/?>
      <div class="copyright">Keep it simplest 
        <div class="fb-like" data-href="http://cssr.ru/simpliste/ru.html" data-send="true" data-layout="button_count" data-width="100" data-show-faces="false" data-font="arial"></div>
      </div>
      <ul class="menu_bottom">
        <li><a href="index.html">En</a></li>
        <li class="active"><a href="#about">Описание</a></li>
        <li><a href="#skins">Темы</a></li>
        <li><a href="#samples">Примеры</a></li>          
      </ul>
    </footer>
    
  </div>
  <?=$absolute;?>
</body>
</html>