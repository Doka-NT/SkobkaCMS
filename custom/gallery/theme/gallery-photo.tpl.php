<?php
$img = $preset > 0?Imageui::GetImage($preset, $file->filepath, $file->original_name):'<img src="'.Path::Url($file->filepath).'" alt="'.$file->original_name.'"/>';
?><span class="gallery-photo">
   <?=Theme::Render('link',$file->filepath,$img,array('class'=>'gallery-image lightbox','rel'=>$group_id));?>
</span>