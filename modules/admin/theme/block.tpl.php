<?php
if ($block->collapse) {
    $collapse_control = 'data-toggle="collapse" data-target="#' . $block->block_id . '-content"';
    if ($block->collapsed)
        $collapse_target = ' collapse';
    else
        $collapse_target = 'in collapse';
}
?><div id="<?= $block->block_id; ?>" class="block">
<? if ((User::Access('Управление блоками')) && (preg_match('/block-custom-(.*)/', $block->block_id))): ?>
        <div class="block-control">
            <?= Theme::Render('link', 'admin/block/block/' . $block->block_id, 'Редактировать'); ?>
        </div>
    <? endif; ?>
    <? if ($block->show_title == 1): ?>
        <h6 class="block-title" <?= $collapse_control; ?>><?= $block->title; ?></h6>
    <? endif; ?>
    <div id="<?= $block->block_id . '-content'; ?>" class="block-content<?= $collapse_target; ?>"><?= $block->content; ?></div>
</div>
