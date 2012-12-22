<div class="control-links">
    <?foreach($aLinks as $path=>$title):?>
        <?=Theme::Render('link',$path,$title);?>
    <?endforeach;?>
</div>