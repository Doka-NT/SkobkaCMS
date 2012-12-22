<?php

class Event {
		private static $aCallStack = array();
		
		/**PUBLIC*/
		public static function Bind($sEvent,$sCallback,$aArguments = array()){
			Event::$aCallStack[$sEvent][] = (object)array(
				'callback'	=>	$sCallback,
				'arguments'	=>	$aArguments
			);
		}
		
		public static function Call($sEvent,&$arg = null){			
			if(array_key_exists($sEvent,Event::$aCallStack))
				foreach(Event::$aCallStack[$sEvent] as $aCall){
					call_user_func_array($aCall->callback, array(&$arg));
				}
		}
		
}