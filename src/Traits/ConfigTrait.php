<?php

/**
 * Get application configuration
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Traits;

use Laminas\Cli\ContainerResolver;
use Laminas\Mvc\Application;
use RuntimeException;

trait ConfigTrait
{
    /**
     * Get container configuration
     *
     * @throws RuntimeException
     */
    protected function getApplicationConfig(): array
    {
        // Services
        if (! $container = ContainerResolver::resolve()) {
            throw new RuntimeException("Configuration file is not provided");
        }
        if (! $container->get('config')) {
            throw new RuntimeException("Configuration data is not provided");
        }
        return $container->get('config');
    }

    /**
     * Retrieve the configured routes
     *
     * @param array|object $applicationConfig
     * @throws RuntimeException
     */
    protected function getRoutesFromConfig($applicationConfig): array
    {
        $router    = $this->getFoundChild('router', $applicationConfig);
        $allRoutes = $this->getFoundChild('routes', $router);
        
        if (! $allRoutes) {
            throw new RuntimeException("Routes are not defined in configuration file.");
        }
        return $allRoutes;
    }
}
