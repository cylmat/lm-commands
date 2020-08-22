<?php

namespace LmConsole;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),

            // For unit testing framework
            'view_manager' => [
                'exception_template'       => 'error/index',
                'template_map' => [
                    'layout/layout'           => __DIR__ . '/error.phtml',
                    'error/404'               => __DIR__ . '/error.phtml', 
                    'error/index'             => __DIR__ . '/error.phtml',
                ]
            ]
        ];
    }

    public function getCliConfig(): array
    {
        return [
            'commands' => [
                'debug:routes [module]' => Command\DebugRoutesCommand::class,
            ]
        ];
    }
}
