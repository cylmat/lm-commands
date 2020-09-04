<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole;

use LmConsole\Model\ModuleCommandLoader;

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
        if (! $commandsList = ModuleCommandLoader::getModulesCommands()) {
            return [];
        }

        // List of all modules commands
        return [
            'commands' => $commandsList
        ];
    }
}
