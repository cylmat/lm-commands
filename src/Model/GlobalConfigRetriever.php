<?php

namespace LmConsole\Model;

class GlobalConfigRetriever
{
    const GLOBAL_REDUNDANCE_AVOIDER = 'GLOBAL_REDUNDANCE_AVOIDER';

    /**
     * @throws \DomainException when modules.config.php not found
     * @todo Use laminas loader too
     */
    public static function getModulesPath(): array
    {
        $globalConfig = self::getGlobalConfig();

        if(gettype($globalConfig)!=='object') {
            return [];
        }

        // Get Application modules list
        $ref = new \ReflectionClass(\Laminas\ServiceManager\ServiceManager::class);
        $d = $ref->getProperty('services');
        $d->setAccessible(true);

        $value = $d->getValue($globalConfig);
        if (!isset($value['ApplicationConfig']['modules'])) {
            throw new \DomainException("Can't find loaded modules, did you provide a modules.config.php file?");
        }
        $modules  = $value['ApplicationConfig']['modules'];

        // Get modules path
        $autoload = self::getComposerAutoload();

        $prefixes = $autoload->getPrefixesPsr4();
        $paths = [];
        foreach ($modules as $moduleName) {
            $paths[$moduleName] = $prefixes[$moduleName . '\\'][0];
        }

        return $paths;
    }

    /* private */

    /**
     * @return \Composer\Autoload\ClassLoader
     * @throws \DomainException when vendor/autoload.php not found
     */
    private static function getComposerAutoload(): \Composer\Autoload\ClassLoader
    {
        $included = \get_included_files();
        
        foreach ($included as $fileName) {
            if (preg_match("/^.*vendor[\\/\\\]autoload.php$/", $fileName, $match)) {
                $path = $match[0];
            }
        }
        
        if (!isset($path)) {
            throw new \DomainException("vendor/autoload.php not found.");
        }

        return include $path;
    }
    
    /**
     * @return null|\Laminas\ServiceManager\ServiceManager
     */
    private static function getGlobalConfig(): ?\Laminas\ServiceManager\ServiceManager
    {   
        /**
         * Avoid redundances with ContainerResolver::resolve()
         */
        $GLOBALS[self::GLOBAL_REDUNDANCE_AVOIDER] = true;
        $config = \Laminas\Cli\ContainerResolver::resolve();
        return is_object($config) ? $config : null;
    }
}