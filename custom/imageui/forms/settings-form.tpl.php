<?
    foreach(Imageui::PresetList() as $preset)
        $row[] = array(
            $preset->id,
            Theme::Render ('link','admin/imageui/edit/'.$preset->id,$preset->title),
            Theme::Render('link-confirm','admin/imageui/delete/'.$preset->id,'Удалить'),
        );
?>
<?=Theme::Render('table',$row,array('id','Метка','Действия'));?>
<?=Theme::Render('input','text','preset[title]','Название настройки',$title);?>