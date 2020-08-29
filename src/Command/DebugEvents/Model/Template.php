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
     * Display all events
     */
    public function displayTemplate(array $eventsList): void
    {
        $output = '';
        foreach ($eventsList as $eventName => $eventProperties) {
            $output .= $this->getEventTemplate($eventName, $eventProperties);
        }

        // Final render
        $this->output->writeln($output);
    }

    /* protected */

    /**
     * Get template for one single event
     */
    protected function getEventTemplate(string $eventName, array $eventProperties): string
    {
        /*
         * info colors:  black, red, green, yellow, blue, magenta, cyan and white.
         * info options: bold, underscore, blink, reverse
         */
        $leftSize   = 10;
        $centerSize = 40; //Default value
        $pipe       = '|';

        // Get max propertie text size
        foreach ($eventProperties as $priority => $callable) {
            if (strlen($callable) > $centerSize) {
                $centerSize = strlen($callable); 
            }
        }

        // Align with head
        $centerSize += 2; // count with '()' size

        // Display event name
        $head = ' [' . $eventName . ']' . PHP_EOL;

        // Display head bar
        $head .= $this->getPatternLine($leftSize, $centerSize);
        $head .= $this->getTextLine(" Priority ", $leftSize, " Callable ", $centerSize);
        $head .= $this->getPatternLine($leftSize, $centerSize);

        // Display events properties
        $main = '';
        foreach ($eventProperties as $priority => $callable) {
            $main .= $this->getTextLine(" $priority ", $leftSize, " $callable ", $centerSize);
        }
        $main .= $this->getPatternLine($leftSize, $centerSize) . PHP_EOL;
        return $head . $main;
    }
}
