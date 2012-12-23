<div class="file-upload-item">
    <i class="icon icon-file"></i> <?= Theme::Render('link', $file->filepath, $file->name, array('target' => '_blank', 'class' => 'file-upload-item')); ?>&nbsp;<span class="delete-file pointer" title="Удалить файл"><i class="icon icon-trash"></i></span>
</div>