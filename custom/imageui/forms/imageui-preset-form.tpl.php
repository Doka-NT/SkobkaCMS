<?php
$preset = Imageui::Load(Path::Arg(3));

?>
<h4>Примеры настроек:</h4>
<pre>
    //Изменит изображение до 100 пикселей по ширине и обрежет до 50 по высоте
    $image = Image::ResizeCrop($image,100,50); 
    //Пропорционально изменит размеры до 100 пикселей по ширине 
    $image = Image::Resize($image,100);
</pre>
<p><?=Theme::Render('link','http://cms.skobka.com/docs/module/imageui','Как пользоваться?');?></p>
<?=Theme::Render('input','textarea','preset_code','Код настройки',$preset->code?:'<?php' . PHP_EOL)?>
<?=Editor::LoadCode('preset_code', 'php');?>