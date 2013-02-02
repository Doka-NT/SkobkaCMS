<?php
global $pdo;

$positions = Theme::GetPositions();
$_positions[0] = 'Не показывать';
foreach ($positions as $position)
    $_positions[$position] = $position;
$positions = $_positions;

$q = $pdo->query("SELECT * FROM blocks b ORDER BY b.weight");
$db_block = array();
while ($oBlock = $pdo->fetch_object($q)) {
    $db_block[$oBlock->block_id] = $oBlock;
    if (preg_match('/block-custom-(\d+)/', $oBlock->block_id))
        $oBlock->is_custom = 1;
}
jQueryUI::Load();
?>
<script type="text/javascript">
    var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index)
        {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    $(function(){
        var item = $('#block-list-table tbody');
        item.parent().find('tr').prepend('<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>');
        //item.parent().find('td.weight, th.weight').hide();
        item.sortable({
            //placeholder: 'ui-state-highlight sortable-target',
            helper: fixHelperModified,
            stop: function(event,ui){
                item.find('tr').each(function(i,v){
                    $(v).find('td.weight input').val(i);
                });
            }
            /*start: function (event, ui) {
                                ui.placeholder.html('<td class="ui-sortable-placeholder" style="height:33px;"></td>');
                        },*/			
        }).disableSelection();
		
		
    });
</script>
<table class="table table-condenced table-striped" id="block-list-table">
    <thead>
    <th>Заголовок</th>
    <th>Позиция</th>
    <th class="weight">Порядок</th>
    <th></th>
</thead>
<tbody>
    <? foreach ($blocks as $block_id => $block): ?>
        <?
        if ($db_block[$block_id])
            $block = $block + (array) $db_block[$block_id];
        ?>
        <tr <?= (!$block['position'] ? 'class="error"' : ''); ?> >
            <td style="text-align:left;"><?= $block['title']; ?></td>
            <td width="200"><?= Theme::Render('select', 'block[' . $block_id . '][position]', '', $positions, $block['position']); ?></td>
            <td width="60" class="weight"><?= Theme::Render('input', 'text', 'block[' . $block_id . '][order]', '', ' ' . (int) $db_block[$block_id]->weight . ' ', array('style' => 'width:20px;text-align:center;')); ?></td>
            <td width="100">
                <a href="#" class="admin-block-settings"><i class="icon icon-wrench"></i> Настроить</a>
                <div class="block-list-settings">
                    <?= Theme::Render('input', 'textarea', 'block[' . $block_id . '][pages]', 'Показывать на страницах', $block['pages']); ?>
                    <?= Theme::Render('input', 'textarea', 'block[' . $block_id . '][not_pages]', 'Скрыть на страницах', $block['not_pages']); ?>
                    <?= Theme::Render('radio', 'block[' . $block_id . '][show_title]', 'Заголовок блока', array(-1 => 'Скрыть', 1 => 'Показать'), $block['show_title']); ?>
                </div>
            </td>
            <td>
                <?if($block['is_custom']):?>
                <?=Theme::Render('link-confirm','admin/block/delete/'.$block['block_id'],'Удалить');?>
                <?endif;?>
            </td>
        </tr>
    <? endforeach; ?>
</tbody>
</table>

<?=
Theme::Render('form-actions', array(
    'submit' => array('text' => 'Сохранить'),
    'html' => array('value' => Theme::Render('link', 'admin/block/add', 'Добавить', array('class' => 'btn btn-danger')))
));
?>