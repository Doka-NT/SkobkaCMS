<?foreach($options as $value=>$label):?>
<label>
    <input type="checkbox" name="<?=$name;?>[<?=$value;?>]" <?=Theme::Attr($attributes);?> <?if(in_array($value, $values)) echo 'checked';?> />
    <span class="checkbox-label"><?=$label;?></span>
</label>
<? endforeach; ?>
