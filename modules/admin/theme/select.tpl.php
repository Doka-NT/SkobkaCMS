<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
<select name="<?=$name;?>" id="<?=$name;?>" <?=Theme::Attr($attributes);?>>
	<?foreach($options as $opt_value=>$option):?>
		<option value="<?=$opt_value;?>" <?if($opt_value == $value) echo 'selected'; elseif(is_array($value)) if(in_array($opt_value,$value)) echo 'selected';?> ><?=$option;?></option>
	<?endforeach;?>
</select>