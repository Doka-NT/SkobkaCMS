<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?= $head_title; ?></title>
        <?= $head; ?>
        <?= $css; ?>
        <?= $js; ?>
    </head>
    <body>
        <div class="page">
            <div class="header">
                <div class="header1">
                    <a href="/" id="logo"><img src="/themes/skobkacms/images/logo.png" alt="SkobkaCMS"/></a>
                    <div class="inner top"><?= $top; ?></div>
                </div>
                <?if($user1):?>
                <div class="header2">
                    <div class="inner">
                        <?= $user1; ?>
                    </div>
                </div>
                <?endif;?>
                <?if($user2):?>
                <div class="header3">
                    <div class="inner">
                        <?= $user2; ?>
                    </div>
                    <div class="user3"><?= $user3; ?></div>
                </div>
                <?endif;?>
            </div>
            <div class="content-top">
                <?= $content_top; ?>
            </div>
            <div class="main">
                <?if($sidebar):?><div class="sidebar"><?= $sidebar; ?></div><?endif;?>
                <div class="main-content">
                    <h1><?=$title;?></h1>
                    <?= $messages; ?>
                    <?= $content; ?>
                </div>
            </div>
            <?if($GLOBALS['user']->uid == 1):?>
                <div style="clear:both;padding:20px;border-top:1px solid #aaa;"><?=Admin::RuntimeInfo();?></div>
            <?endif;?>
            <div class="footer">
                <?= $footer; ?>
		<div class="copyright" style="text-align: right;position: absolute;right: 20px;top: 10px;"><a href="http://skobkacms.ru" target="_blank">Powered by SkobkaCMS</a></div>
            </div>
        </div>
        <?= $absolute; ?>
    </body>
</html>