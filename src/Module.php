<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole;

use LmConsole\Config\ModuleCommandLoader;

class Module
{
    public function getConfig(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig()
        ];
    }

    protected function getCliConfig(): array
    {
        if (! $commands = ModuleCommandLoader::getModulesCommands()) {
            return [];
        }

        // Get list of all modules commandes
        // Retrieve COMMAND [arguments] list
        $commandsList = [];
        foreach ($commands as $command) {
            $key                  = $command::getDefaultName(); 
            $commandsList[ $key ] = $command;
        }

        // List of all modules commands
        return [
            'commands' => $commandsList
        ];
    }
}
