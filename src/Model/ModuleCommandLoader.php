<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Model;

use Laminas\Code\Reflection\FileReflection;
use LmConsole\Command\AbstractCommand;
use LmConsole\Config\GlobalConfig;
use LmConsole\Model\CommandCache;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleCommandLoader
{
    /**
     * Get commands from all loaded modules
     * Look into each files for a LmConsole\Command\AbstractCommand extended class
     */
    public static function getModulesCommands(): array
    {
        $cacheDir = GlobalConfig::getApplicationConfig()['cache_dir'];
        $cache = new CommandCache($cacheDir);

        $sha = self::getModulesSha();
        $shaKey = 'shaModuleConfig';

        // Check for cache if modules list havn't changed
        if($cache->has()) {
            $cachedResult = $cache->get();

            // Remove what is not commands array
            if(is_array($cachedResult) && array_key_exists($shaKey, $cachedResult)) {

                // Return result only if sha doesn't changed (no modules changed)
                if ($cachedResult[$shaKey] === $sha) {
                    unset($cachedResult[$shaKey]);
                    echo 'CAHCED';
                    return $cachedResult;
                }
            }
        }

        // Get list
        $commandsList = $commandsListWithSha = self::getCommandsList();
        $commandsListWithSha[$shaKey] = $sha;

        // Set in cache commands and sha
        $cache->set($commandsListWithSha);

        return $commandsList;
    }

    protected static function getCommandsList(): array
    {
        // Configuration from Resolver
        // Avoid to load all modules two times
        if (GlobalConfig::isResolverLoaded()) {
            return [];
        }

        $modulesPath  = GlobalConfig::getModulesPath();
        $commands = [];

        // Look into each module directory
        // Search for all *Command.php files inside
        foreach ($modulesPath as $path) {
            $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            
            // Look for each php file
            foreach ($directoryIterator as $file) {
                if ($file->isDir()) {
                    continue;
                }

                if (! preg_match('/^.+Command.php$/', $file->getFilename())) {
                    continue;
                }

                $fileReflection = new FileReflection($file->getRealpath(), true);

                // One class for one file
                $class = $fileReflection->getClasses()[0];
                if (AbstractCommand::class === $class->getParentClass()->getName()) {
                    $commands[] = $class->getName();
                }
            }
        }

        // Get list of all modules commandes
        // Retrieve COMMAND [arguments] list
        $commandsList = [];
        foreach ($commands as $command) {
            $key                  = $command::getDefaultName(); 
            $commandsList[ $key ] = $command;
        }

        return $commandsList;
    }

    protected static function getModulesSha(): string
    {
        $modulesList = GlobalConfig::getApplicationConfig()['modules'];
        return sha1(serialize($modulesList));
    }
}
