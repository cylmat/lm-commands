<?php

/**
 * Laminas EventManager extended to retrieve events list
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugEvents\Model;

use Laminas\EventManager\EventManager;

class EventDebuggerManager extends EventManager
{
    /** @var array */
    protected static $static_events;

    /**
     * Return all events with pattern:
     *
     * Event_name
     *      Priority1 => Callable1
     *      Priority2 => Callable2
     *      Priority3 => Callable3
     */
    public function getEventsList(?string $eventName = null): array
    {
        $events = [];

        // Get all events loaded into manager
        foreach (self::$static_events as $e_name => $e_params) {
            // Skip others events if event_name is provided
            if ($eventName && $e_name !== $eventName) {
                continue;
            }

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
                        if (is_object($class)) {
                            $invokedClass                  = get_class($class) . '()';
                            $events[$e_name][$currentPriority++] .= $invokedClass;
                        } elseif (is_array($class)) {

                            // Array, events stacked
                            // Increment priority for each stacked event of same priority
                            foreach ($class as $n => $label) {
                                // n:0 for class
                                // n:1 for method
                                $classOrMethod = is_string($label) ? $label . '()' : get_class($label) . '::';
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
     * {@inheritDoc}
     * phpcs:disable WebimpressCodingStandard.Functions
     */
    public function attach($eventName, $listener, $priority = 1)
    {
        self::$static_events[$eventName][(int) $priority][0][] = $listener;
        return parent::attach($eventName, $listener, $priority);
    }

    /**
     * {@inheritDoc}
     * phpcs:disable WebimpressCodingStandard.Functions
     */
    public function trigger($eventName, $target = null, $argv = [])
    {
        return parent::trigger($eventName, $target, $argv);
    }

    /**
     * {@inheritDoc}
     * phpcs:disable WebimpressCodingStandard.Functions
     */
    public function triggerListeners($event, $callback = null)
    {
        return parent::triggerListeners($event, $callback);
    }
}
