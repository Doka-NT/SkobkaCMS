<?php
Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'jquery.autocomplete.js');
Theme::AddJs(Module::GetPath('admin') . DS . 'js' . DS . 'autocomplete.js');
$attributes['class'] .= ' autocomplete2';
$attributes['data-url'] = Path::Url($callback);
$attributes['placeholder'] = 'Начните вводить текст';
?>
<?if($label):?><label for="<?=$name;?>"><?=$label;?></label><?endif;?>
<input type="text" name="<?=$name;?>" value="<?=$value;?>" <?=Theme::Attr($attributes);?> />