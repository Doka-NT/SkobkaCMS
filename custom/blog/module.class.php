<?php

class Blog {
    
    public function Init(){
        Module::IncludeFile('blog','BlogEvent.class.php');
        BlogEvent::Init();
    }

    public static function EventProfileLoad($profile){
        $profile->fields[100] = array(
            'value'=>Theme::Render('link','blog/personal/'.$profile->uid,'<i class="icon-book"></i>&nbsp;Персональный блог'),
        );
    }
    
    public static function AddResourse(){
        Theme::AddCss(Module::GetPath('blog') . DS . 'css' . DS . 'blog.css');
        Theme::AddJs(Module::GetPath('blog') . DS . 'js' . DS . 'blog.js');
        Theme::AddJsSettings(array(
            'blog'=>array(
                'vote_callback'=>'ajax/blog/voting',
            )
        ));
    }
    
    public function Rules() {
        return array(
            'Администрировать блоги',
            'Создавать блоги',
            'Публиковать записи',
        );
    }

    public function Menu() {
        return array(
            'admin/blog' => array(
                'title' => 'Настройки',
                'callback' => 'BlogAdmin::SettingsPage',
                'file' => 'BlogAdmin',
                'rules' => array('Администрировать блоги'),
            ),
            'blog/add/blog' => array(
                'title' => 'Создать блог',
                'callback' => 'BlogPages::AddBlog',
                'file' => 'BlogPages',
                'rules' => array('Создавать блоги'),
            ),
            'blog/add/post' => array(
                'title' => 'Создать публикацию',
                'callback' => 'BlogPages::AddPost',
                'file' => 'BlogPages',
                'rules' => array('Публиковать записи'),
            ),            
            'ajax/blog/autocomplete' => array(
                'type' => 'callback',
                'callback' => 'Blog::AjaxAutocomplete',
            ),
            'blog' => array(
                'title' => 'Блог',
                'callback' => 'BlogPages::BlogMain',
                'file' => 'BlogPages',
            ),
            'blog/post' => array(
                'title' => 'Запись блога',
                'type'  =>  'callback',
                'callback' => 'BlogPages::BlogPostPage',
                'file' => 'BlogPages',
            ),
            'blog/list' =>  array(
                'title' =>  'Список блогов',
                'callback'  =>  'BlogPages::BlogList',
                'file'  =>  'BlogPages',
            ),
            'blog/drafts'=>array(
                'title' =>  'Черновики',
                'callback'  =>  'BlogPages::PageDrafts',
                'file'      =>  'BlogPages',
            ),
            'blog/personal'=>array(
                'title' =>  'Персональный блог',
                'callback'  =>  'BlogPages::PagePersonal',
                'file'      =>  'BlogPages',
            ),            
            'ajax/blog/voting' =>   array(
                'type'  =>  'callback',
                'callback'  =>  'Blog::AjaxVoting',
            )
        );
    }
    
    public function BlockInfo(){
        return array(
            'block-blog-actions'=>array(
                'title'=>'Блоги',  
                'content'=>Blog::BlockBlogs(),
            )
        );
    }

    public static function Theme(){
        return array(
            'blog-post-rate-widget'=>array(
                'type'=>'template',
                'template'  =>  Module::GetPath('blog') . DS . 'theme' . DS . 'blog-post-rate-widget.tpl.php',
                'arguments' =>  array('post'=>NULL),
            )
        );
    }
    
    public static function AjaxAutocomplete() {
        global $pdo;
        $q = $pdo->query("SELECT * FROM blogs WHERE title LIKE ?", array('%' . $_GET['query'] . '%'));
        $match = array();
        while ($blog = $pdo->fetch_object($q)) {
            $match[$blog->id] = $blog->title;
        }
        echo json_encode(array(
            'query' => $_GET['query'],
            'suggestions' => array_values($match),
            'data' => array_keys($match),
        ));
        exit;
    }
    
    public static function AjaxVoting(){
        global $pdo,$user;
        $id = $_POST['post_id'];
        $votes = $pdo->fetch_object($pdo->query("SELECT COUNT(*) as votes FROM blog_votes WHERE post_id = ? AND uid = ?",array($id,$user->uid)))->votes;
        if($votes)
            $error = "Вы уже голосовали за эту публикацию";
        else {
            $pdo->insert('blog_votes',array(
                'post_id' => $id,
                'uid'   => $user->uid,
                'vote' => $_POST['vote'] > 1?0: ($_POST['vote'] < -1 ? 0 : $_POST['vote'] ),
            ));
        }
        $rate = $pdo->fetch_object($pdo->query("SELECT SUM(vote) as vote FROM blog_votes WHERE post_id = ?",array($id)))->vote;
        $pdo->query("UPDATE blog_post SET rate = ? WHERE id = ?",array($rate,$id));
        Core::Json(array(
           'rate' =>    (int)$rate,
           'error'  =>  $error?$error:null
        ));
    }
    
    public static function LoadBlog($blog_id, $reload = false) {
        global $pdo, $blog_objects;
        if ($reload || !$blog_objects[$blog_id]) {
            $blog = $pdo->fetch_object($pdo->query("SELECT * FROM blogs WHERE id = ?", array($blog_id)));
            $blog->path = 'blog/'.$blog_id;
            Event::Call('BlogLoad',$blog);
            $blog_objects[$blog_id] = $blog;
        }
        return $blog_objects[$blog_id];
    }

    public static function LoadPost($post_id) {
        global $pdo;
        $post = $pdo->fetch_object($pdo->query("SELECT * FROM blog_post WHERE id = ?",array($post_id)));
        if(!$post->id)
            return false;
        $post = Blog::SplitBlogPost($post);
        $post->path = 'blog/post/'.$post->id;
        $post->author = User::Load($post->uid);
        if($post->blog_id > 0)
            $post->blog = Blog::LoadBlog($post->blog_id);
        elseif($post->blog_id == 0)
            $post->blog = Blog::LoadPersonal($post);
        else
            $post->blog = Blog::LoadDrafts ($post);
        Event::Call('LoadPost',$post);
        
        return $post;
    }

    public static function LoadPersonal($post){
        $blog = (object)array(
            'id'=>0,
            'title'=>'Персональный блог - '.$post->author->name,
            'path'=>'blog/personal/'.$post->author->uid,
        );
        return $blog;
    }
    
    public static function LoadDrafts($post){
        $blog = (object)array(
            'id'=>-1,
            'title' => 'Черновики',
            'path'  =>  'blog/drafts/'.$post->author->uid,
        );
        return $blog;
    }
    
    public static function SplitBlogPost($post) {
        $aText = explode("<!-- pagebreak -->", $post->content);
        $post->teaser = $aText[0];
        return $post;
    }

    public static function PostTemplate(){
        return Module::GetPath('blog') . DS . 'theme' . DS . 'blog-post.tpl.php';
    }
    
    public static function BuildPost($post,$is_page = true){
        $post = Blog::SplitBlogPost($post);
        $post->author = User::Load($post->uid);
        if($post->blog_id < 0)
            $post->blog = Blog::LoadDrafts($post);        
        elseif($post->blog_id == 0)
            $post->blog = Blog::LoadPersonal($post);
        else
            $post->blog = Blog::LoadBlog($post->blog_id);
        $post->path = 'blog/post/'.$post->id;
        
        $post->is_post_page = $is_page;
        $post->teaser = Blog::PostLink($post) . $post->teaser;
        $post->content = Blog::PostLink($post) . $post->content;
        return $post;
    }
    
    public static function BlockBlogs(){
        global $pdo,$user;
        Blog::AddResourse();
        $q = $pdo->QueryLimit("SELECT blogs.* FROM blogs WHERE blogs.parent = 0",null,0,10);
        $out = '';
        while($blog = $pdo->fetch_object($q)){
            $out .= '<div class="blog-item">'.Theme::Render('link','blog/'.$blog->id,$blog->title).'</div>';
        }
        $out .= '<div class="blogs-all">'.Theme::Render('link','blog/list','Все блоги').'</div>';
        $aLinks = array(
            User::Access('Администрировать блоги')?Theme::Render('link','blog/add/blog','Создать блог'):'',
            $user->uid?Theme::Render('link','blog/add/post','<i class="icon-pencil"></i> Написать'):'',
            $user->uid?Theme::Render('link','blog/drafts','<i class="icon-file"></i> Черновики'):'',
        );
        return $out .= '<div class="blog-actions">'.implode('',$aLinks).'</div>';
    }
    
    public static function EmptyBlog(){
        return 'Данный блог не содержит записей';
    }
    
    public static function GetPostAddOptions($default = 0,$value = false){
        global $pdo;
        $q = $pdo->query("SELECT * FROM blogs WHERE open = 1 AND parent = 0");
        $options = array(
            -1  =>  'Черновики',
            0   =>  'Персональный блог'
        );
        while($blog = $pdo->fetch_object($q)){
            $options[$blog->id] = $blog->title;
        }
        return Theme::Render('select','blog','Поместить в',$options,$value === false?array($default):$value);
    }
    
    public static function PostTips(){
        $tags = explode(" ",Variable::Get('blog_settings_tags',Blog::DefaultAllowedTags()));
        $tips = array();
        foreach($tags as $tag)
            $tips[] = ''.$tag.'';
        return '<div class="blog-post-tips"><h6>Доступны следующие теги для оформления текста</h6><div class="blog-post-tips-list">'.implode(", ",$tips).'</div></div>';
    }
    
    public static function DefaultAllowedTags(){
        $tags = 'a img b i strong em div p span h2 h3 h4 h5 h6 pre'; 
        return $tags;
    }
    
    public static function PrepareStripTags($tags){
        $tags = explode(" ",$tags);
        
        foreach($tags as $k=>$tag)
            $tags[$k] = '<'.$tag.'>';
        return implode("",$tags);
    }
    
    public static function AccessPost($post){
        global $user;
        if(User::Access('Администрировать блоги') || $post->author->uid == $user->uid)
            return true;
        return false;
    }
    
    public static function PostLink($post){
        if(!Blog::AccessPost($post))
            return;
        
        return '<div class="blog-post-link">'.Theme::Render('link','blog/post/'.$post->id.'/edit','<i class="icon-pencil"></i> Редактировать') . Theme::Render('link-confirm','blog/post/'.$post->id.'/delete','<i class="icon-trash"></i> Удалить') . '</div>';
    }
}