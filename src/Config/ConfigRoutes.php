<?php

/**
 * Get application configuration
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Config;

use Laminas\Cli\ContainerResolver;
use Laminas\Mvc\Application;
use LmConsole\Traits\ToolsTrait;
use RuntimeException;
use LmConsole\Config\GlobalConfig;

class ConfigRoutes
{
    use ToolsTrait;

    /**
     * Retrieve the configured routes for all modules
     *
     * @throws RuntimeException
     */
    public function getAllRoutes(): array
    {
        $globalConfig = GlobalConfig::getGlobalConfig();

        $router    = $this->getFoundChild('router', $globalConfig);
        $allRoutes = $this->getFoundChild('routes', $router);
        
        if (! $allRoutes) {
            throw new RuntimeException("Routes are not defined in configuration file.");
        }
        return $allRoutes;
    }
}
