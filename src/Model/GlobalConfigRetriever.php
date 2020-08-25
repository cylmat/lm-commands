<?php

/**
 * Get global configuration file to retrieve list of all modules
 * 
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Model;

use Composer\Autoload\ClassLoader;
use DomainException;
use Laminas\Cli\ContainerResolver;
use Laminas\ServiceManager\ServiceManager;
use ReflectionClass;

class GlobalConfigRetriever
{
    public const GLOBAL_REDUNDANCE_AVOIDER = 'GLOBAL_REDUNDANCE_AVOIDER';

    /**
     * @throws DomainException When modules.config.php not found.
     * @todo Use laminas loader too
     */
    public static function getModulesPath(): array
    {
        $globalConfig = self::getGlobalConfig();

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
    
    /**
     * Retrieve the config resolver
     */
    private static function getGlobalConfig(): ?ServiceManager
    {
        /**
         * Avoid redundances with ContainerResolver::resolve()
         */
        $GLOBALS[self::GLOBAL_REDUNDANCE_AVOIDER] = true;
        $config = ContainerResolver::resolve();
        return is_object($config) ? $config : null;
    }
}
