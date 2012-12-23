CMS.ui.attach.fileUpload = function(){
    var all_input = $('input.file-upload');
    all_input.each(function(){
        var input = $(this);
        input.change(function(){
            var el = $(this);
            var val = $(this).val();
            var bar = $('.upload-bar');
            
            var preview_area = $(this).parents('.file-upload-wrapper').find('.preview .preview-new');
            if(!val)
                return;
            bar.empty().html('<div class="progress"><div class="bar"></div></div>');
            bar = $('.upload-bar .bar');
            $.upload5(CMS.settings.web_root + 'file_upload',this,function(data,code){
                if(code != 200){
                    CMS.ui.modal('Не удалось загрузить файл');
                    return;
                }
                data = JSON.parse(data);
                if(!data.status){
                    CMS.ui.modal('Загрузка файла не удалась. Попробуйте еще раз.');
                    return;
                }
                if(data.fmessage)
                    CMS.ui.modal(data.fmessage);
                if(data.message)
                    CMS.ui.modal(Base64.decode(data.message));
                preview_area.html(data.data);
                el.val('');
            },function(percent,bu,bt){
                bar.css('width',percent + '%');
                bu = parseInt(bu/1024);
                bt = parseInt(bt/1024);
                bar.text(bu + 'кб / ' + bt + 'кб');
            });
        });        
    });
    
    $('.delete-file').live('click',function(){
        var link = $(this).parent().find('a.file-upload-item');
        $(this).after('<input type="hidden" name="delete_file[]" value="'+ link.attr('href') + '" />');
        link.parent().css({opacity:0.5});
    });
}