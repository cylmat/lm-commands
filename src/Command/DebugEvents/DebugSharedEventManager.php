<?php

namespace LmConsole\Model\DebugEvents;

use Laminas\EventManager\SharedEventManager;

class DebugSharedEventManager extends SharedEventManager
{
    public function debug()
    {
        $events = $this->events;
        foreach ($events as $n => $e) {
           
        }
    } 
}