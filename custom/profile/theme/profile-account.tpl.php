<div class="profile">
    <h2 class="personal">
        <?=implode(" ",array(
            $account->profile->surname,
            $account->profile->name,
            $account->profile->middlename
        ));?>
    </h2> 
    <?if($account->profile->occupation):?>
    <div class="profile-item">
        <span class="pih">Род занятий:</span><span class="piv"><?=$account->profile->occupation;?></span>
    </div>
    <?endif;?>
    <?if($account->profile->homepage):?>
    <? 
        $parse = parse_url($account->profile->homepage);
    ?>
    <div class="profile-item">
        <span class="pih"><img src="http://<?=$parse['host'];?>/favicon.ico" width="16" height="16"/></span><span class="piv"><?=Theme::Render('link',$account->profile->homepage,$account->profile->homepage);?></span>
    </div>
    <?endif;?>
    <?=Theme::Render('profile-picture',$account);?>       
</div>