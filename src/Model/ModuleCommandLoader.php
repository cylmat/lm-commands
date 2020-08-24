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
        $modulesPath  = GlobalConfigRetriever::getModulesPath();
        $commandsList = [];

        // Look into each module directory
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
                    $commandsList[] = $class->getName();
                }
            }
        }
        return $commandsList;
    }
}
