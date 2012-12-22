<?php
class CommentEvent {
    public static function EventContentView(&$aVars){
        $oContent = &$aVars['content'];
        $types = Variable::Get('comment_types');
        if((!$types)||(!is_array($types)))
            return;
        if(!in_array($oContent->type, $types))
                return;
        $oContent->data .= Comment::Get('content',$oContent->id); 
    }
    
    public static function EventNotificationList(&$aList){
        $aList['comment-reply'] = 'Уведомлять об ответах на мои комментарии';
        $aList['blog-comment-my-topic'] = "Уведомлять о новых комментариях в моих топиках";
    }
    
    public static function EventCommentAdd($comment){
        if(!$comment->parent){
            if($comment->object == 'blog/post'){
                $post = Blog::LoadPost($comment->object_id);
                if($post->author->uid == $comment->uid)
                    return; //Предотвращаем отправку уведомлений о своих комментариях
                global $root_host;
                $comment_author = User::Load($comment->uid);
                $post_url = $root_host . Path::Url($post->path,1);
                $body = 'Пользователь '. Theme::Render('link',$root_host . Path::Url('user/'.$comment_author->uid,1),$comment_author->name) 
                        .' оставил новый комментарий к вашей теме "<b>'.$post->title.'</b>".<br>Прочитать его можно по ссылке ниже:<br>'
                        .Theme::Render('link',$post_url,$post_url);
                Notification::Send('blog-comment-my-topic','Новый комментарий к вашему топику',$body,$post->author->mail);
            }
            return;
        }
        $parent = Comment::Load($comment->parent);
        if($comment->uid != $parent->uid){
            //Отправляем уведомление
            $host = 'http://' . $_SERVER['SERVER_NAME'] . $GLOBALS['web_root'];
            $path = Path::GetAlias($comment->object . DS . $comment->object_id);
            $url = $host . $path;
            $comment_author = User::Load($parent->uid);
            $reply_author = User::Load($comment->uid);
            $body = 'Пользователь '.$reply_author->name.' ответил на ваш комментарий. <br>Прочитать его можно по следующей ссылке:<br>' . Theme::Render('link',$url, $url);
            Notification::Send('comment-reply', 'Вам ответили на комментарий', $body,$comment_author->mail);
        }
    }
}