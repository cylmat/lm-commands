<?php

/**
 * Retrieve configuration and events
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugRoutes;

use LmConsole\Command\DebugRoutes\{Config, Template};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Factory
{
    public static function getConfig(InputInterface $input): Config
    {
        return new Config($input);
    }
    
    public static function getTemplate(OutputInterface $output): Template
    {
        return new Template($output);
    }
}
