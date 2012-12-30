<?
    foreach(Imageui::PresetList() as $preset)
        $row[] = array(
            $preset->id,
            Theme::Render ('link','admin/imageui/edit/'.$preset->id,$preset->title),
            Theme::Render('link-confirm','admin/imageui/delete/'.$preset->id,'Удалить'),
        );
?>
<?=  ImageUI::GetImage(1, 'themes/skobkacms/images/logo.png');?>
<?=Theme::Render('table',$row);?>
<?=Theme::Render('input','text','preset[title]','Название настройки',$title);?>