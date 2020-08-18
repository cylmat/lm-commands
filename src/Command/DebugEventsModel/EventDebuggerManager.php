<?php

namespace LmConsole\Command\DebugEventsModel;

use Laminas\EventManager\EventManager;

/**
 * Event manager which can debug events list
 */
class EventDebuggerManager extends EventManager
{
    static protected $static_events;

    /**
     * Return all events
     */
    public function getEventsList(): array
    {
        $events = [];

        // Get all events loaded into manager
        foreach(self::$static_events as $e_name => $e_params) {
            $events[$e_name] = [];
            
            // Sort by priority asc
            // to increase listeners with stacked array
            ksort($e_params);

            // Load each listeners
            foreach ($e_params as $e_priority => $e_listeners) {

                // Load listener
                $currentPriority = $e_priority;
                foreach ($e_listeners as $e_listener) {
                    foreach ($e_listener as $class) {

                        $events[$e_name][$currentPriority] = $events[$e_name][$currentPriority] ?? "";

                        // Object __invoke()
                        if(is_object($class)) {
                            $invokedClass = get_class($class) . '()';
                            $events[$e_name][$e_priority] .= $invokedClass;

                        } elseif(is_array($class)){

                            // Array, events stacked
                            // Increment priority for each stacked event of same priority
                            foreach ($class as $n => $label) {

                                // n:0 for class
                                // n:1 for method
                                $classOrMethod = (is_string($label) ? $label . '()' : get_class($label) . '::');
                                $events[$e_name][$currentPriority] .= $classOrMethod;
                            }
                            $currentPriority++;
                        } 

                        // Sort each event by priority desc
                        // higher launched first
                        krsort($events[$e_name]);
                    }
                }
            }
        }
        
        return $events;
    }
    
    /**
     * @inheritDoc
     */
    public function attach($eventName, $listener, $priority = 1)
    {
        //echo 'attach '.$eventName."\n";

        self::$static_events[$eventName][(int) $priority][0][] = $listener;

        //echo ' : '.(is_object($listener)?get_class($listener):get_class($listener[0]).'[Array]').'<br/>';
        return parent::attach($eventName, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function trigger($eventName, $target = null, $argv = [])
    {
        //echo 'trigger '.$eventName . "\n";
        //echo $this->getIdentifiers();
        return parent::trigger($eventName, $target, $argv);
    }

    /**
     * @inheritDoc
     */
    public function triggerListeners( $event, $callback = null)
    {
        //echo 'triggerListeners '.$event->getName() . "\n";
        return parent::triggerListeners($event, $callback);
    }
}

