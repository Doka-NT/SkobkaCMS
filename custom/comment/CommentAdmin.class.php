<?php
class CommentAdmin {
    public static function CommentSettings(){
        return Form::GetForm('CommentAdmin::CommentSettingsForm');
    }
    
    public static function CommentSettingsForm(){
        return array(
            'id'        =>  'comment-settings-form',
            'type'      =>  'template',
            'template'  =>  Module::GetPath('comment') . DS . 'forms' . DS . 'comment-settings-form.tpl.php',
            'standart'  =>  TRUE,
        );
    }
    
    public static function CommentList(){
        global $pdo;
		$q = $pdo->query("SELECT * FROM comments ORDER BY created DESC");
		$row = array();
		while($comment = $pdo->fetch_object($q)){
			$row[] = array(
				$comment->cid,
				Theme::Render('link',$comment->object . DS . $comment->object_id, substr($comment->content,0,80)),
				date('d.m.Y - H:i',$comment->created),
			);
		}
		return Theme::Render('table',$row,array(
			'cid','Комментарий','Опубликован',
		));
    }    
}