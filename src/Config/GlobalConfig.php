<?php

/**
 * Get global configuration file to retrieve list of all modules
 * Used for autoloading Commands
 * 
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Config;

use Composer\Autoload\ClassLoader;
use DomainException;
use Laminas\Cli\ContainerResolver;
use Laminas\ServiceManager\ServiceManager;
use ReflectionClass;
use RuntimeException;

class GlobalConfig
{
    private const CONTAINER_LOADED = 'CONTAINER_LOADED';

    public static function isResolverLoaded(): bool
    {
        if (! isset($GLOBALS[self::CONTAINER_LOADED])) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve the config file
     */
    public static function getApplicationConfig(): array
    {
        // Retrieve configuration
        $appConfig = require 'config/application.config.php';
        if (file_exists('config/development.config.php')) {
            $appConfig = \Laminas\Stdlib\ArrayUtils::merge($appConfig, require 'config/development.config.php');
        }
        return $appConfig;
    }

    /**
     * Retrieve the config resolver
     */
    public static function getGlobalServiceManager(): ?ServiceManager
    {
        /**
         * Avoid redundances with ContainerResolver::resolve()
         */
        $GLOBALS[self::CONTAINER_LOADED] = true;

        $config = ContainerResolver::resolve();
        $config = is_object($config) ? $config : null;

        return $config;
    }

    /**
     * @throws DomainException When modules.config.php not found.
     */
    public static function getModulesPath(): array
    {
        $globalConfig = self::getGlobalServiceManager();

        if (gettype($globalConfig) !== 'object') {
            return [];
        }

        // Get Application modules list
        $ref = new ReflectionClass(ServiceManager::class);
        $d   = $ref->getProperty('services');
        $d->setAccessible(true);

        $value = $d->getValue($globalConfig);
        if (! isset($value['ApplicationConfig']['modules'])) {
            throw new DomainException("Can't find loaded modules, did you provide a modules.config.php file?");
        }
        $modules = $value['ApplicationConfig']['modules'];

        // Get modules path
        $autoload = self::getComposerAutoload();

        $prefixes = $autoload->getPrefixesPsr4();
        $paths    = [];
        foreach ($modules as $moduleName) {
            $paths[$moduleName] = $prefixes[$moduleName . '\\'][0];
        }

        return $paths;
    }

    /**
     * Get container configuration from all modules
     *
     * @throws RuntimeException
     */
    public static function getGlobalConfig(): array
    {
        // Services
        if (! $container = ContainerResolver::resolve()) {
            throw new RuntimeException("Configuration file is not provided");
        }
        if (! $config = $container->get('config')) {
            throw new RuntimeException("Configuration data is not provided");
        }
        return $config;
    }

    /* private */

    /**
     * Get the ClassLoader object
     * 
     * @throws DomainException When vendor/autoload.php not found.
     */
    private static function getComposerAutoload(): ClassLoader
    {
        $included = get_included_files();
        
        foreach ($included as $fileName) {
            if (preg_match("/^.*vendor[\\/\\\]autoload.php$/", $fileName, $match)) {
                $path = $match[0];
            }
        }
        
        if (! isset($path)) {
            throw new DomainException("vendor/autoload.php not found.");
        }

        return include $path;
    }
}
