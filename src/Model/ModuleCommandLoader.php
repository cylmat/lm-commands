<?php

namespace LmConsole\Model;

class ModuleCommandLoader
{
    /**
     * Get commands from all loaded modules
     * Look into each files for a LmConsole\Command\AbstractCommand extended class
     * 
     * @return array
     */
    public static function getModulesCommands(): array
    {
        $modulesPath = \LmConsole\Model\GlobalConfigRetriever::getModulesPath();
        $commandsList = [];

        // Look into each module directory
        foreach ($modulesPath as $path) {
            $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            
            // Look for each php file
            foreach ($directoryIterator as $file) {
                if ($file->isDir()) continue;

                if (!preg_match('/^.+Command.php$/', $file->getFilename())) continue;

                $fileReflection = new \Laminas\Code\Reflection\FileReflection($file->getRealpath(), true);

                // One class for one file
                $class = $fileReflection->getClasses()[0];
                if ('LmConsole\Command\AbstractCommand' == $class->getParentClass()->getName()) {
                    $commandsList[] = $class->getName();
                }
            }
        }
        return $commandsList;
    }
}