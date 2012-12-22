<?php

class Meta {

    /*public function Rules() {
        return array('Использовать мета-теги');
    }

    public function Menu() {
        return array(
        );
    }*/

    public function Init() {
        Event::Bind('ContentView', 'Meta::EventContentView');
    }

    public static function EventContentView(&$args) {
        Event::Bind('LoadHeadInfo', 'Meta::EventLoadHeadInfo');
        $GLOBALS['meta_keywords'] = Meta::GenerateKeywords(strip_tags($args['content']->data));
        $GLOBALS['meta_description'] = Meta::GenerateDescription($args['content']->data);
    }

    public static function EventLoadHeadInfo(&$meta) {
        global $meta_keywords, $meta_description;
        if($meta_keywords){
            Event::Call('MetaKeywordsAlter',$meta_keywords);
            $meta .= '<meta name="keywords" content="'.$meta_keywords.'"/>';
        }
        if($meta_description){
            Event::Call('MetaDescriptionAlter',$meta_description);
            $meta .= '<meta name="description" content="'.$meta_description.'"/>';
        }
    }

    public static function GenerateKeywords($text, $word_len = 3, $quantity = 15) {
        //$text = preg_replace("/[^а-яs-]/isu", "", strtolower($text));
		$text = strip_tags($text);
        $_del_symbols = array("как", "для", "что", "или", "это", "этих", "потому", "поэтому", "просто", "очень","для",
            "всех", "они", "оно", "еще", "когда", "тогда", "которые", "того",
            "где", "эта", "лишь", "уже", "вам", "нет",
            "если", "надо", "все", "так", "его", "чем",
            "при", "даже", "мне", "есть", "раз", "два",
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
			"&nbsp;",
        );
		
        foreach ($_del_symbols as $val) {
            $del_symbols[] = "/" . $val . "/";
        }
        $text = preg_replace($del_symbols, "", $text);
        
        
        preg_match_all("/[а-яА-Яa-zA-Z]{" . $word_len . ",}/us", $text, $word);

        //$return = array_flip(array_count_values($word[0])); //получаем слова и частоту, меняем местами ключ-значение

        $keywords_count = array_count_values($word[0]);
        $keywords_result = array();
        foreach ($keywords_count as $k => $v) {
            if ($v > 1) {
                $keywords_result[$v][] = $k;
            }
        }
        if (sizeof($keywords_result) < 1)
            return false;

        krsort($keywords_result);

        foreach ($keywords_result as $key => $value) {
            foreach ($value as $key => $word) {
                $keywords[] = $word;
            }
        }

        $keywords = array_slice($keywords, 0, $quantity);
        return join(",", $keywords);
    }

    public static function GenerateDescription($text,$length = 250){
        return str_replace(array("\n","\r"),' ',substr(strip_tags($text), 0, $length));
    }
}