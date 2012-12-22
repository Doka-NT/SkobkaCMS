<?=Theme::Render('input','text','title','Название',$post->title);?>
<?=Blog::GetPostAddOptions(0,$post->blog->id);?>
<?=Theme::Render('input','textarea','content','Текст поста',$post->content);?>
<script type="text/javascript">
    var onChangeCallback = function(){
        $(function(){
            $('textarea#content').val(tinyMCE.activeEditor.getContent());
        });
    }
</script>
<?=Blog::PostTips();?>
<?=Theme::Render('form-actions',array(
    'submit'=>array('text'=>'Опубликовать'),
));?>