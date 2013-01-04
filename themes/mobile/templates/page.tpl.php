<!DOCTYPE html> 
<html> 
    <head> 
        <title><?= $head_title; ?></title> 
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
        <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
        <?= $head; ?>
        <?= $css; ?>
    </head> 
    <body> 
        <div class="page" data-role="page">
            <h1 data-role="header"><?= $title; ?></h1>
            <? if ($messages): ?>
                <div class="messages"></div>
            <? endif; ?>
            <div class="page-content" data-role="content"><?= $content; ?></div>
        </div>
        <?=$absolute;?>
        <?=$js;?>
    </body>
</html>