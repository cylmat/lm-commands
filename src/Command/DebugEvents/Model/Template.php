<?php

/**
 * Debug events used in Laminas MVC Module
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugEvents\Model;

use LmConsole\Traits\DisplayTrait;
use Symfony\Component\Console\Output\OutputInterface;

class Template
{
    use DisplayTrait;

    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Display all events wih properties
     */
    public function displayFullEvents(array $eventsList): void
    {
        $output = '';
        foreach ($eventsList as $eventName => $eventProperties) {
            $output .= $this->getEventPropertiesTemplate($eventName, $eventProperties);
        }

        // Final render
        $this->output->writeln($output);
    }

    /**
     * Display only list
     */
    public function displayEventsList(array $eventsList): void
    {
        $output = $this->getEventListTemplate($eventsList);

        // Final render
        $this->output->writeln($output);
    }

    /* protected */

    /**
     * Get template for one single event
     */
    protected function getEventPropertiesTemplate(string $eventName, array $eventProperties): string
    {
        /*
         * info colors:  black, red, green, yellow, blue, magenta, cyan and white.
         * info options: bold, underscore, blink, reverse
         */
        $leftSize   = 10;
        $centerSize = 40; //Default value
        $pipe       = '|';

        // Get max propertie text size
        $centerSize = $this->getMaxLength($eventProperties);

        // Align with head
        $centerSize += 2; // count with '()' size

        // Display event name
        //$head = "\n" . ' [' . $eventName . ']' . PHP_EOL;
        $this->displayTop($eventName);

        // Display head bar
        $head = $this->displayHead(" Priority ", $leftSize, " Callable ", $centerSize);

        // Display events properties
        $main = '';
        foreach ($eventProperties as $priority => $callable) {
            $main .= $this->getTextLine(" $priority ", $leftSize, " $callable ", $centerSize);
        }
        $main .= $this->getPatternLine($leftSize, $centerSize) . PHP_EOL;
        return $main;
    }

    /**
     * Get template for a list of events with --list option
     */
    protected function getEventListTemplate(array $eventList): string
    {
        /*
         * info colors:  black, red, green, yellow, blue, magenta, cyan and white.
         * info options: bold, underscore, blink, reverse
         */
        $leftSize   = 50;
        $pipe       = '|';

        // Get max propertie text size
        $centerSize = $this->getMaxLength($eventList, 0);

        // Align with head
        $centerSize += 2; // count with '()' size

        $head = '';

        // Display head bar
        $head .= $this->getPatternLine($leftSize);
        $head .= $this->getTextLine(" Name ", $leftSize);
        $head .= $this->getPatternLine($leftSize);

        // Display events name
        $main = '';
        foreach ($eventList as $name => $properties) {
            $main .= $this->getTextLine(" $name ", $leftSize);
        }
        $main .= $this->getPatternLine($leftSize) . PHP_EOL;
        return $head . $main;
    }
}
