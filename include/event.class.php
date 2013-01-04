<?php

class Event {

    private static $aCallStack = array();

    /*     * PUBLIC */

    /**
     * Производит прикрепление $sCallback к стеку вызова при событии $sEvent
     * @param type $sEvent
     * @param type $sCallback
     * @param type $aArguments
     */
    public static function Bind($sEvent, $sCallback, $aArguments = array()) {
        Event::$aCallStack[$sEvent][] = (object) array(
                    'callback' => $sCallback,
                    'arguments' => $aArguments
        );
    }

    /**
     * Вызывает определенное событие
     * @param type $sEvent 
     * имя события
     * @param type $arg
     * аргументы для передачи в callback
     */
    public static function Call($sEvent, &$arg = null) {
        global $oEngine;
        /**
         * Вызываем сначала события привязанные через public $Event{EventName}, 
         * где {EventName} = $sEvent
         */
        if ($oEngine->modules)
            foreach ($oEngine->modules as $oModule) {
                $attr = 'Event' . $sEvent;
                if ($callback = $oModule->$attr)
                    call_user_func_array($callback, array(&$arg));
            }

        if (array_key_exists($sEvent, Event::$aCallStack))
            foreach (Event::$aCallStack[$sEvent] as $aCall) {
                call_user_func_array($aCall->callback, array(&$arg));
            }
    }

}