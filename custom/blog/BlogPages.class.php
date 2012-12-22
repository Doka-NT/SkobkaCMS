<?php

class BlogPages {
    public static function BlogMain(){
        Blog::AddResourse();
        if($blog_id = (int)Path::Arg(1))
            if(Path::Arg(2) == 'delete')
                return BlogPages::BlogDelete($blog_id);
            elseif(Path::Arg(2) == 'edit')
                return BlogPages::EditBlog($blog_id);
            else
                return BlogPages::BlogPage($blog_id);
        $template_post = Blog::PostTemplate();
        global $pdo;
        $per_page = Variable::Get('blog_post_per_page',10);
        $q = $pdo->PagerQuery($sql = "SELECT * FROM blog_post WHERE blog_id NOT IN (-1) ORDER BY created DESC",null,$per_page);
        $out = '';
        while($post = $pdo->fetch_object($q)){
            $post = Blog::BuildPost($post,false);
            $out .= Theme::Template($template_post,array('post'=>$post));
        }
        return $out . Theme::Pager($sql,null,$per_page);
    }
    
    public static function BlogDelete($blog_id){
        if(!User::Access('Администрировать блоги'))
            return Menu::NotFound ();
        global $pdo;
        $pdo->query("UPDATE blog_post SET blog_id = 0 WHERE blog_id = ?",array($blog_id));//Перемещаем записи в персональный блог
        $pdo->query("DELETE FROM blogs WHERE id = ?",array($blog_id));
        Notice::Message('Блог удален');
        Path::Back();
    }
    
    public static function EditBlog($blog_id){
        if(!User::Access('Администрировать блоги'))
            return Menu::NotFound();
        $blog = Blog::LoadBlog($blog_id);
        return Form::GetForm('BlogPages::FormAddBlog',$blog);
    }
    
    public static function BlogPage($blog_id){
        global $pdo;
        $per_page = Variable::Get('blog_post_per_page',10);
        $q = $pdo->PagerQuery($sql = "SELECT * FROM blog_post WHERE blog_id = ? ORDER BY created DESC",$args = array($blog_id),$per_page);
        $out = '';
        $blog = Blog::LoadBlog($blog_id);
        Theme::SetTitle($blog->title);
        if(!$q->rowCount())
            return Blog::EmptyBlog ();        
        while($post = $pdo->fetch_object($q)){
            $post = Blog::BuildPost($post,false);
            $out .= Theme::Template(Blog::PostTemplate(),array('post'=>$post));
        }
        return $out . Theme::Pager($sql,$args,$per_page);
    }


    public static function AddBlog(){
        if(User::Access('Администрировать блоги'))
            return Form::GetForm('BlogPages::FormAddBlog');
    }
    
    public static function FormAddBlog(){
        return array(
            'id'    =>  'form-add-blog',
            'type'  =>  'template',
            'template'  =>  Module::GetPath('blog') . DS . 'forms' . DS . 'form-add-blog.tpl.php',
            'required'  =>  array('title'),
            'arguments' =>  array('blog'=>NULL),
            'submit'    =>  array('BlogPages::FormAddBlogSubmit'),
        );
    }
    
    public static function FormAddBlogSubmit(&$aResult){
        global $pdo;
        $blog = $aResult[1];
        if(!$blog->id){
            $blog = array(
                'title' => $_POST['title'],
                'description'   =>  $_POST['description'],
                'open'          =>  $_POST['open'],
                
            );
            $pdo->insert('blogs',$blog);
            if($alias = $_POST['alias']){
                $blog_id = $pdo->lastInsertId();
                Path::AddAlias('blog/'.$blog_id, $alias);
            }            
            Notice::Message('Блог '.$blog['title'].' создан.');
        }
        else {
            $pdo->query("UPDATE blogs SET title = ?, description = ?, open = ?, parent = ? WHERE id = ?",array(
               $_POST['title'],
               $_POST['description'],
               $_POST['open'],
               (int)$_POST['parent'],
               $blog->id,
            ));
            $alias = $_POST['alias'];
            if(!$alias)
                Path::DeleteAlias($blog->path);
            else{
                Path::DeleteAlias($blog->path);
                Path::AddAlias($blog->path, $alias);
            }
            Notice::Message('Блог обновлен');
            $aResult['replace'] = 'blog';
        }
    }
    
    public static function BlogPostPage(){
        global $user;
        Blog::AddResourse();
        $post_id = Path::Arg(2);
        $post = Blog::LoadPost($post_id);
        if(!$post->id)
            return Menu::NotFound ();
        if($act = Path::Arg(3))
            if($act == 'edit')
                return BlogPages::BlogPostEdit($post);
            elseif($act == 'delete')
                return BlogPages::BlogPostDelete($post);
            else
                return Menu::NotFound ();
        $post->is_post_page = true;
        $post = Blog::BuildPost($post);
        if(($post->blog->id < 0)&&(($post->author->uid != $user->uid)||(!User::Access('Администрировать блоги'))))
                return Menu::NotFound();
        Event::Call('BlogPostPage',$post);
        Theme::SetTitle($post->title);
        return Theme::Template(Blog::PostTemplate(), array('post'=>$post)) . Comment::Get('blog/post', $post_id);
    }
    
    public static function AddPost(){
        if(!User::Access('Публиковать записи'))
            return Menu::AccessDenied ();
        return Form::GetForm('BlogPages::FormAddPost');
    }
    
    public static function FormAddPost($post = NULL){
        Core::LoadModule('tinymce');
        tinymce::Load(false);
        Theme::AddJs(Module::GetPath('blog') . DS . 'js' . DS . 'blog-post-form.js');
        return array(
            'id' => 'form-add-blog-post',
            'type'  =>  'template',
            'template'  =>  Module::GetPath('blog') . DS . 'forms' . DS . 'form-add-blog-post.tpl.php',
            'required'  =>  array('title','content'),
            'arguments' =>  array('post'=>NULL),
            'submit'    =>  array('BlogPages::FormAddPostSubmit'),
        );
    }
    
    public static function FormAddPostSubmit(&$aResult){
        $post = $aResult[1];
        $allowed_tags = Variable::Get('blog_settings_tags',Blog::DefaultAllowedTags()) . '<!-- pagebreak -->';
        $text = strip_tags($_POST['content'], Blog::PrepareStripTags($allowed_tags));
        if(empty($text))
            return Notice::Error ('После чистки от кода, текст оказался пуст');
        if($post->id)
            return BlogPages::FormEditPostSubmit($aResult);
        global $pdo,$user;
        $blog_id = $_POST['blog'];
        $pdo->insert('blog_post',array(
            'blog_id'=>$blog_id,
            'title' =>strip_tags(htmlspecialchars($_POST['title'])),
            'content'=>$text,
            'created'=>time(),
            'uid'=>$user->uid,
        ));
        $post_id = $pdo->lastInsertId();
        $added_post = Blog::LoadPost($post_id);
        Event::Call('BlogPostAdd',$added_post);
        if($post_id){
            if($blog_id > 0){
                $blog = Blog::LoadBlog($blog_id);
                Path::AddAlias('blog/post/'.$post_id, Path::GetAlias($blog->path) . '/' . $post_id . '.html');
            }
            $aResult['replace'] = 'blog/post/'.$post_id;
            return;
        }
        Notice::Error('Системная ошибка при добавлении записи. Обратитесь к администратору.');
    }
    
    public static function BlogPostDelete($post){
        if(!Blog::AccessPost($post))
            return Menu::NotFound ();
        global $pdo;
        $pdo->query("DELETE FROM blog_post WHERE id = ?",array($post->id));
        Notice::Message('Публикация "'.$post->title.'" удалена');
        Path::Replace('blog');
    }
    
    public static function BlogPostEdit($post){
        global $user;
        $bool = $user->uid && (User::Access('Администрировать блоги') || ($user->uid == $post->author->uid));
        if($bool)
            return Form::GetForm('BlogPages::FormAddPost',$post);
        return Menu::NotFound();
    }
    
    public static function FormEditPostSubmit(&$aResult){
        global $pdo;
        $pdo->query("UPDATE blog_post SET blog_id = :blog_id, title = :title, content = :content WHERE id = :id",array(
            'blog_id' => $_POST['blog'],
            'title'=>$_POST['title'],
            'content'=>$_POST['content'],
            'id'=>$aResult[1]->id,
        ));
        Notice::Message('Публикация была сохранена.');
        $aResult['replace'] = 'blog/post/'.$aResult[1]->id;
    }
    
    public static function BlogList(){
        Blog::AddResourse();
        global $pdo;
        $q = $pdo->query("SELECT * FROM blogs WHERE open = 1");
        $out = '';
        while($blog = $pdo->fetch_object($q)){
            $out .= Theme::Template(Module::GetPath('blog') . DS . 'theme' . DS . 'blog-list-item.tpl.php',array('blog'=>$blog));
        }
        return $out;
    }
    
    public static function PageDrafts(){
        global $pdo,$user;
        if(!$user->uid)
            return Menu::NotFound ();
         $per_page = Variable::Get('blog_post_per_page',10);
        $q = $pdo->PagerQuery($sql = "SELECT * FROM blog_post WHERE blog_id = -1 AND uid = ?",$args = array($user->uid),$per_page);
        $out = '';
        if(!$q->rowCount())
            return Blog::EmptyBlog ();        
        while($post = $pdo->fetch_object($q)){
            $post = Blog::BuildPost($post,false);
            $out .= Theme::Template(Blog::PostTemplate(),array('post'=>$post));
        }        
        return $out . Theme::Pager($sql, $args, $per_page);
    }
    
    public static function PagePersonal(){
        global $pdo,$user;
        $account = User::Load(Path::Arg(2));
        if(!$account->uid)
            return Menu::NotFound ();
        Theme::SetTitle('Персональный блог '.$account->name);
        $per_page = Variable::Get('blog_post_per_page',10);
        $q = $pdo->PagerQuery($sql = "SELECT * FROM blog_post WHERE blog_id = 0 AND uid = ?",$args = array($account->uid),$per_page);
        $out = '';
        if(!$q->rowCount())
            return Blog::EmptyBlog ();        
        while($post = $pdo->fetch_object($q)){
            $post = Blog::BuildPost($post,false);
            $out .= Theme::Template(Blog::PostTemplate(),array('post'=>$post));
        }        
        return $out . Theme::Pager($sql, $args, $per_page);      
    }
}