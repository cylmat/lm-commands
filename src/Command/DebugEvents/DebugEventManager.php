<?php

namespace LmConsole\Model\DebugEvents;

use Laminas\EventManager\EventManager;

class DebugEventManager extends EventManager
{
    static protected $static_events;

    public function debug()
    {
        //d($this->sharedManager);
        //d($this->identifiers);  //application

        $events = self::$static_events;
        foreach($events as $e_name => $e_params) {

            echo('<b>'.$e_name.'</b>') . ':' . (is_object($e_params) ? get_class($e_params) : '').'<br/>';
            //d($e_params);
            krsort($e_params);
            foreach ($e_params as $e_priority => $e_listeners) {
                echo " . . . Priority: ".$e_priority.'<br/>';

                foreach ($e_listeners as $k) {
                    foreach ($k as $b) {
                        /*if($e_name === 'try.service.launched') {
                            d($b);
                        }*/
                        if(is_object($b)) {
                            echo (is_string($b) ? $b. '<br/>' : ' . . . . . . '.get_class($b).' :: ');
                        }
                        foreach ($b as $aaa => $bbb) {
                            echo (is_string($bbb) ? $bbb. '<br/>' : ' . . . . . . '.get_class($bbb).' :: ');
                            
                        }
                    }
                }
            }
            echo '<br/>';
        }
    }

    /*public function trigger($eventName, $target = null, $argv = [])
    {
        //echo 'trigger '.$eventName.'<br/>';
        return parent::trigger($eventName, $target, $argv);
    }*/

    public function attach($eventName, $listener, $priority = 1)
    {
        echo 'attach '.$eventName.'';

        self::$static_events[$eventName][(int) $priority][0][] = $listener;

        echo ' : '.(is_object($listener)?get_class($listener):get_class($listener[0]).'[Array]').'<br/>';
        return parent::attach($eventName, $listener, $priority);
    }

    /*protected function triggerListeners( $event, $callback = null)
    {
        echo 'triggerListeners '.$event->getName().'<br/>';
        return parent::triggerListeners($event, $callback);
    }*/
}