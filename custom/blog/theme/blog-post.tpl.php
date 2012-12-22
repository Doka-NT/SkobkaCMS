<div class="blog-post">
    <?if(!$post->is_post_page):?>
    <h5 class="blog-post-title"><?=Theme::Render('link',$post->path,$post->title);?> <span class="blog-rarr">&larr;</span> <?=Theme::Render('link',$post->blog->path,$post->blog->title);?></h5>
    <?endif;?>
    <div class="blog-post-content">
        <?if($post->is_post_page):?>
            <?=$post->content;?>
        <?else:?>
            <?=$post->teaser;?>
            <?=Theme::Render('link',$post->path,'Читать полностью');?>
        <?endif;?>
    </div>
    <div class="blog-post-info">
        <span class="blog-post-info-author"><i class="icon-user"></i> <?=Theme::Render('link','user/'.$post->uid,$post->author->name);?></span>
        <span class="blog-post-info-created"><?=date('d.m.Y',$post->created);?></span>
        <span class="blog-post-info-blog"><i class="icon-book"></i> <?=Theme::Render('link',$post->blog->path,$post->blog->title);?></span>
        <span class="blog-post-info-comments"><?=Theme::Render('blog-post-rate-widget',$post);?></span>
    </div>
</div>