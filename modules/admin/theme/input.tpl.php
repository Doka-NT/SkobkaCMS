<?php
	$attributes['class'] .= ' form-'.$type;
	/*if(($type == 'textarea')&&($attributes['php']))
		Editor::LoadPhp();*/
	if(($type == 'textarea')&&($attributes['editor']))
		Editor::Load();		
	$_attributes = ' name="'.$name.'" id="'.$name.'" '.Theme::Attr($attributes);
?>
<?if($type == 'text'):?>
	<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
	<input type="text" <?=$_attributes;?> <?=($value !== null?'value="'.$value.'"':'');?> />
<?endif;?>
<?if($type == 'number'):?>
	<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
	<input type="text" <?=$_attributes;?> value="<?=(float)$value;?>" />
<?endif;?>
<?if($type == 'password'):?>
	<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
	<input type="password" <?=$_attributes;?> <?=$value?'value="'.$value.'"':'';?> />
<?endif;?>
<?if($type == 'textarea'):?>
	<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
	<textarea <?=$_attributes;?>><?=$value;?></textarea>
	<?if($attributes['editor']):?>
            <?if($attributes['mode'] == 'php'):?>
                <?=Editor::LoadCode($name, $attributes['mode']?$attributes['mode']:'html', $name);?>
            <?else:?>
                <?=  Editor::Load();?>
            <?endif;?>
        <?endif;?>
<?endif;?>
<?if($type == 'submit'):?>
	<input type="submit" value="<?=$value;?>" name="<?=$name;?>" <?=$_attributes;?> />
<?endif;?>
<?if($type == 'checkbox'):?>
	<?if($label):?>
		<label for="<?=$name;?>">
			<input type="checkbox" name="<?=$name;?>" <?=$value?'checked="checked"':'';?> <?=$_attributes;?> />
			<span><?=$label;?></span>
		</label>
	<?else:?>
		<input type="checkbox" name="<?=$name;?>" <?=$value?'checked="checked"':'';?> <?=$_attributes;?> />
	<?endif;?>
<?endif;?>