<div class="attached-files">
    <h5>Прикрепленные файлы</h5>
    <?foreach($files as $index=>$file):?>
    <div class="attached-files file-<?=$index;?>">
        <i class="icon icon-file"></i> <?=Theme::Render('link','files/download/'.$file->id,$file->original_name);?>
    </div>
    <?endforeach;?>
</div>