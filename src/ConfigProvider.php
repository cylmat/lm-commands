<?php

namespace MyConsole;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getCliConfig(): array
    {
        return [
            'commands' => [
                'debug:routes [module]'         => Command\DebugRoutes::class,

                'model:generate:crud'           => Command\ModelGenerateCrud::class,
                'model:generate:access'         => Command\ModelGenerateAccess::class,

                'module:create'   => Command\ModuleCreate::class
            ]
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                
            ]
        ];
    }
}