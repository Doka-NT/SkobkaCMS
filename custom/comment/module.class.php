<?php
class Comment {
    public function Rules(){
        return array('Видеть комментарии','Комментрировать','Управлять настройками комментариев','Управлять комментариями');
    }
    
    public function Init(){
        include_once Module::GetPath('comment') . DS . 'CommentEvent.class.php';
        Event::Bind('ContentView','CommentEvent::EventContentView');
        Event::Bind('NotificationList','CommentEvent::EventNotificationList');
        Event::Bind('CommentAdd','CommentEvent::EventCommentAdd');
    }
    
    public function Menu(){
        return array(
            'admin/comments/settings'=>array(
                'title'     =>  'Настройки комментариев',
                'callback'  =>  'CommentAdmin::CommentSettings',
                'file'      =>  'CommentAdmin',
                'rules'     =>  array('Управлять настройками комментариев'),
            ),
            'admin/comments/list'=>array(
                'title'     =>  'Список комментариев',
                'callback'  =>  'CommentAdmin::CommentList',
                'file'      =>  'CommentAdmin',
                'rules'     =>  array('Управлять комментариями'),
            ),
            'comment/delete'=>array(
                'type'      =>  'callback',
                'callback'  =>  'Comment::DeleteCallback',
                'rules'     =>  array('Управлять комментариями'),
            ),
            'comment/edit'=>array(
                'type'      =>  'callback',
                'callback'  =>  'Comment::EditCallback',
                'rules'     =>  array('Управлять комментариями'),
            ),            
        );
    }
    
    public static function Load($cid){
        global $pdo;
        return $pdo->fetch_object($pdo->query("SELECT * FROM comments WHERE cid = ?",array($cid)));
    }

    public static function DeleteCallback(){
        $cid = Path::Arg(2);
        self::_Delete($cid);
        Notice::Message('Комментарий и его ветка удалены');
        Path::Back();
    }
    
    private static function _Delete($cid){
        global $pdo;
        $q = $pdo->query("SELECT * FROM comments WHERE parent = ?",array($cid));
        while($comment = $pdo->fetch_object($q)){
            self::_Delete($comment->cid);
            $pdo->query("DELETE FROM comments WHERE cid = ?",array($comment->cid));
        }
        $pdo->query("DELETE FROM comments WHERE cid = ?",array($cid));
    }
    
    public static function EditCallback(){
        $cid = Path::Arg(2);
        $oComment = Comment::Load($cid);
        if(!$oComment)
            return Menu::NotFound ();
        return Form::GetForm('Comment::EditForm',$oComment);
    }
    
    public static function EditForm(){
        return array(
            'id'        =>  'comment-edit-form',
            'type'      =>  'template',
            'template'  =>  Module::GetPath('comment') . DS . 'forms' . DS . 'comment-form.tpl.php',
            'arguments' =>  array('comment'=>NULL),
            'submit'    =>  array('Comment::EditSubmit'),
        );
    }
    
    public static function EditSubmit(&$aResult){
        $oComment = $aResult[1];
        $content = nl2br(htmlspecialchars($_POST['comment']));
        global $pdo;
        $pdo->query("UPDATE comments SET content = ? WHERE cid = ?",array($content,$oComment->cid));
        Notice::Message('Комментарий сохранен');
        Path::Replace($oComment->object . '/' . $oComment->object_id);
    }
    
    public static function Get($object,$object_id){
        
        Theme::AddJs(Module::GetPath('comment') . DS . 'js' . DS . 'comment.js');        
        Theme::AddCss(Module::GetPath('comment') . DS . 'css' . DS . 'comment.css');
        $aComments = self::_Get($object, $object_id);
        $out = '<div class="comments-main-wrapper"><h4 class="comments-title">Комментарии</h4>';
        foreach($aComments as $comment)
            $out .= Comment::CommentRender ($comment);
        return $out . self::GetForm($object,0,$object_id).'</div>';
    }
    
    public static function CommentRender($comment){
        if(User::Access('Видеть комментарии'))
            return Theme::Template (Module::GetPath('comment') . DS . 'theme' . DS . 'comment.tpl.php', array('comment'=>$comment), $comment->object );
        return '';
    }
    
    protected static function _Get($object,$object_id,$parent = 0,$status = 1){
        global $pdo;
        $q = $pdo->query("SELECT comments.*, users.name FROM comments LEFT JOIN users ON comments.uid = users.uid WHERE object = :object AND object_id = :object_id AND parent = :parent AND status = :status",array(
            'object'=>$object,
            'object_id'=>$object_id,
            'parent'=>$parent,
            'status'=>$status,
        ));
        $aComments = array();
        while($comment = $pdo->fetch_object($q)){
            $comment->child = self::_Get($object, $object_id,$comment->cid,$status);
            $aComments[$comment->cid] = $comment;
        }
        return $aComments;
    }
    
    public static function GetForm($object,$parent = 0,$object_id = 0){
        global $user;
        if(User::Access('Комментрировать'))
            return Form::GetForm('Comment::CommentForm',$object,$parent,$object_id);
        
        //return '<p class="comment-false">Только '.Theme::Render('link','user/register','зарегистрированные пользователи').' могут оставлять комментарии</p>';
    }
    
    public static function CommentForm(){
        return array(
            'id'        =>  'comment_form',
            'type'      =>  'template',
            'template'  =>  Module::GetPath('comment') . DS . 'forms' . DS . 'comment-form.tpl.php',
            'arguments' =>  array('object'=>NULL,'parent'=>0,'object_id' => 0),
            
            //'ajax'      =>  TRUE,
            'result'    =>  'prepend',
            'required'  =>  array('comment'),
            'submit'    =>  array('Comment::CommentFormSubmit'),
            'sisyphus'  =>  false,
           // 'js_callback'=> 'alert',
        );
    }
    
    public static function CommentFormSubmit(&$aResult){
        global $pdo,$user;
        $data = array(
            'object'=>$_POST['object'],
            'parent'=>$_POST['parent']?$_POST['parent']:0,
            'content'=> nl2br(htmlspecialchars($_POST['comment'])),
            'object_id'=>$aResult[3],
            'created'=>time(),
            'status'=>1,
            'uid'=>$user->uid
        );
        
        $pdo->insert('comments',$data);
        $comment = (object)$data;
        Event::Call('CommentAdd',$comment);
        Notice::Message('Комментарий добавлен');
        //$aResult['js_callback'] = 'comment_callback';
    }
}