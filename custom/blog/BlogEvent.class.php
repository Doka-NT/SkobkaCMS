<?php

class BlogEvent {
    
    public static function Init(){
        Event::Bind('SitemapRebuild','BlogEvent::SitemapRebuild');
        Event::Bind('ProfileLoad','Blog::EventProfileLoad');
        Event::Bind('NotificationList','BlogEvent::EventNotificationList');
        Event::Bind('BlogPostAdd','BlogEvent::EventBlogPostAdd');
    }
    
    public static function SitemapRebuild(&$data) {
        global $pdo;
        $q = $pdo->query("SELECT * FROM blog_post WHERE blog_id >= 0");
        while ($res = $pdo->fetch_object($q)) {
            $data[] = array(
                'loc' => Path::Url('blog/post/' . $res->id),
            );
        }
    }

    public static function EventNotificationList(&$aList){
        $aList['blog-topic-notify'] = 'Уведомлять о новых топиках в блогах';
    }
    
    public static function EventBlogPostAdd($post){
        global $root_host;
        if($post->blog->id == -1) //Если топик не в черновиках
            return;
        
        $body = 'Пользователь '.Theme::Render('link',$root_host . Path::Url('user/'.$post->author->uid,1),$post->author->name).' опубликовал новый топик "<b>'.$post->title .'</b>"<br>'
                . 'Прочесть его можно по ссылке ниже:<br>' . Theme::Render('link',$post->path,$post->path);
        Notification::Send('blog-topic-notify', 'Был добавлен новый топик', $body);
    }
}