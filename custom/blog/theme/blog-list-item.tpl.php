<div class="blog-list-item">
    <h4 class="blog-list-item-title"><i class="icon-book"></i> <?=Theme::Render('link','blog/'.$blog->id,$blog->title);?></h4>
    <div class="blog-list-item-description"><?=$blog->description;?></div>
    <?if(User::Access('Администрировать блоги')):?>
    <div class="blog-list-item-control">
        <?=Theme::Render('link','blog/'.$blog->id.'/edit','<i class="icon-pencil"></i> Редактировать');?>
        <?=Theme::Render('link-confirm','blog/'.$blog->id.'/delete','<i class="icon-trash"></i> Удалить');?>
    </div>
    <?endif;?>
</div>