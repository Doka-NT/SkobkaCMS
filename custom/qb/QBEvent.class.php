<?php
class QBEvent {
	public static function FormLoad(&$aForm){
		//Notice::Message(Theme::Render('dump',$aForm));
	}
        
        public static function QBQueryAlter(&$params){
            $path = Path::QArg();
            //:last
            if(preg_match('/:last/',$params->query)){
                $params->args[':last'] = end($path);
            }
            //:arg_n
            if(preg_match('/:arg_(\d+)/', $params->query,$matches)){
                $arg = $matches[0];
                $arg_no = $matches[1];
                $params->args[$arg] = $path[$arg_no];
            }
            //:time
            if(preg_match('/:time/', $params->query)){
                $params->args[':time'] = time();
            }
            //:uid
            if(preg_match('/:uid/', $params->query)){
                global $user;
                $params->args[':uid'] = $user->uid;
            }
        }
}