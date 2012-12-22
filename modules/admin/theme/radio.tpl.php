<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
<?foreach($options as $ovalue=>$label):?>
	<div><input type="radio" name="<?=$name;?>" value="<?=$ovalue;?>" <?=$ovalue==$value?'checked ':'';?>><?=$label;?></div>
<?endforeach;?>