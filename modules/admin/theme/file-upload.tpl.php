<?php
Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'jquery.upload5.js');
Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'file-upload.js');
?>
    <?if(User::Access('Загружать изображение')):?>
<div id="file-upload-<?=$name;?>" class="file-upload-wrapper">
    <div class="preview">
        <div class="preview-was">
            <?if($value):?>
                <?foreach($value as $file):?>
                    <?=Theme::Render('file-upload-item',$file);?>
                <?endforeach;?>
            <?endif;?>            
        </div>
        <div class="preview-new"></div>
    </div>
    <div class="upload-area">
        <div class="upload-area-inner">Перетащите сюда файл</div>
        <input type="file" name="<?=$name;?>" id="<?=$name;?>" multiple="multiple" class="file-upload"/>
    </div>
    <div class="upload-bar">
        <div class="progress">
            <div class="bar"></div>
        </div>
    </div>
</div>
    <?endif;?>  