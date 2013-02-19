<div class="form-actions">
    <? foreach ($buttons as $type => $button): ?>
	<? if ($type == 'submit'): ?>
	    <input type="submit" value="<?= $button['text']; ?>" class="btn btn-primary" />
	<? endif; ?>
	<? if ($type == 'html'): ?>
	    <?= $button['value']; ?>
	<? endif; ?>
	<? if ($type == 'cancel'): ?>
	    <a href="<?=$_SERVER['HTTP_REFERER']?:Path::Url('frontpage');?>" class="btn"><?=$button['text'];?></a>
	<? endif; ?>
    <? endforeach; ?>
</div>