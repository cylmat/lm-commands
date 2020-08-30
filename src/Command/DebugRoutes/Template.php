<?php

/**
 * Debug events used in Laminas MVC Module
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugRoutes;

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
     * Display routes
     */
    public function displayRoutes(array $definedRoutes): void
    {
        // Get size of columns
        $leftSize = 40; //default value
        $centerSize = 50;
        $rightSize = 50;

        foreach ($definedRoutes as $i => $route) {
            // Left col with route name
            $size = strlen($route['name']);
            if ($size > $leftSize) {
                $leftSize = $size;
            }

            // Center col with others data
            $size = strlen($route['route']);
            if ($size > $centerSize) {
                $centerSize = $size;
            }

            $size = strlen($route['default_controller']);
            if ($size > $rightSize) {
                $rightSize = $size;
            }
        }

        // Align with head
        $leftSize += 2;
        $centerSize += 2;
        $rightSize += 2;

        $head = $main = '';
        
        // Display head bar
        $head = "\n";
        $head .= $this->getPatternLine($leftSize, $centerSize, $rightSize);
        $head .= $this->getTextLine(" Route ", $leftSize, " Url ", $centerSize, " Default ", $rightSize);
        $head .= $this->getPatternLine($leftSize, $centerSize, $rightSize);

        // Display routes properties
        foreach ($definedRoutes as $i => $route) {
            $default = $route['default_controller'] ? $route['default_controller'] : "no default params";
            $main .= $this->getTextLine(
                " {$route['name']} ", $leftSize, 
                " {$route['route']} ", $centerSize,
                " $default ", $rightSize
            );
        }
        $main .= $this->getPatternLine($leftSize, $centerSize, $rightSize) . PHP_EOL;
        
        $this->output->writeln($head . $main);
    }
}