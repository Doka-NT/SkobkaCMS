<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta charset="utf-8" />
        <title><?= $head_title; ?></title>
        <?= $head; ?>
        <?= $css; ?>
        <?= $js; ?>
    </head>
    <body id="top">
        <div id="network">
            <div class="center-wrapper">
                <div class="left"> <?=Date::Get('l, d F, Y');?> <span class="text-separator">|</span> <span class="quiet"><?=$top1;?></span></div>
                <div class="right">
                    <?=$top2;?>
                    <div class="clearer">&nbsp;</div>
                </div>
                <div class="clearer">&nbsp;</div>
            </div>
        </div>
        <div id="site">
            <div class="center-wrapper">
                <div id="header">
                    <div class="right" id="toolbar"><?=$top3;?></div>
                    <div class="clearer">&nbsp;</div>
                    <div id="site-title">
                        <h1><a href="<?=$frontpage;?>"><?=$site_name;?></a> <span> / <?=$title;?></span></h1>
                    </div>
                    <div id="navigation">
                        <div id="main-nav"><?=$nav1;?></div>
                        <div id="sub-nav"><?=$nav2;?></div>
                    </div>
                </div>
                <div class="main" id="main-three-columns">
                    <div class="left" id="main-left">
                        <?=$messages;?>
                        <?=$content;?>
                        <div class="content-separator"></div>
                        <div class="col3 left">
                            <?=$bottom1;?>
                        </div>
                        <div class="col3 col3-mid left">
                            <?=$bottom2;?>
                        </div>
                        <div class="col3 right">
                            <?=$bottom3;?>
                        </div>
                        <div class="clearer">&nbsp;</div>
                    </div>
                    <?if($sidebar1):?>
                    <div class="left sidebar" id="sidebar-1">
                        <?=$sidebar1;?>
                    </div>
                    <?endif;?>
                    <?if($sidebar2):?>
                    <div class="right sidebar" id="sidebar-2">
                       <?=$sidebar2;?>
                    </div>
                    <?endif;?>
                    <div class="clearer">&nbsp;</div>
                </div>
                <div id="dashboard">
                    <div class="column left" id="column-1">
                        <?=$bottom4;?>
                    </div>
                    <div class="column left" id="column-2">
                        <?=$bottom5;?>
                    </div>
                    <div class="column left" id="column-3">
                        <?=$bottom6;?>
                    </div>
                    <div class="column right" id="column-4">
                        <?=$bottom7;?>
                    </div>
                    <div class="clearer">&nbsp;</div>
                </div>
                <div id="footer">
                    <div class="left">&copy; <?=Date::Get('Y');?> <?=$site_name;?> 
                        <div class="footer-content"><?=$footer;?></div>
                    </div>
                    <div class="right">Сайт работает на <a href="http://cms.skobka.com" target="_blank">SkobkaCMS</a></div>
                    <div class="clearer">&nbsp;</div>
                </div>
                <?if($runtime_info):?>
                <div class="runtime_info">
                    <?=$runtime_info;?>
                </div>
                <?endif;?>
            </div>
        </div>
        <?=$absolute;?>
    </body>
</html>