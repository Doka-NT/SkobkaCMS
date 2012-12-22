<div id="comment-<?=$comment->cid;?>" class="comment">
    <div class="comment-info">
        <?=Theme::Render('link','user/'.$comment->uid,$comment->name);?>, <span class="comment-date"><?=date('d.m.Y - H:i',$comment->created);?></span>
        <span class="comment-links">
            <a href="#comment-reply" class="comment-reply-link">Ответить</a>
            <?if(User::Access('Управлять комментариями')):?>
            <?=Theme::Render('link','comment/edit/'.$comment->cid,'Редактировать',array('class'=>'comment-edit-link'));?>
            <?=Theme::Render('link-confirm','comment/delete/'.$comment->cid,'Удалить',array('class'=>'comment-delete-link'));?>
            <?endif;?>
        </span>
    </div>
    <div class="comment-content"><?=$comment->content;?></div>
    <div class="comment-reply"><?=  Comment::GetForm($comment->object, $comment->cid, $comment->object_id);?></div>
    <?foreach($comment->child as $child):?>
        <?=Comment::CommentRender($child);?>
    <?endforeach;?>
</div>