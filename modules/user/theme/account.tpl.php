<div class="account">
    <div class="account-content">
        <? if (User::CheckAccess($account->uid)): ?>
            <ul>
                <li class="links">
                    <?= Theme::Render('link', 'user/' . $account->uid . '/edit', 'Редактировать'); ?>
                </li>
            </ul>
        <? endif; ?>
        <div class="account-inner">
            <?=$prefix;?>
            <? foreach ($account->fields as $value): ?>
                <? if ($value['name']): ?>
                    <div class="account-field-name"><?= $value['name']; ?></div>
                <? endif; ?>
                <? if ($value['value']): ?>
                    <div class="account-field-value"><?= $value['value']; ?></div>
                <? endif; ?>                
            <? endforeach; ?>
            <?=$suffix;?>
        </div>
    </div>
</div>