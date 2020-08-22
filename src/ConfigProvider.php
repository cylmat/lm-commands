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
            ],

            'router' => [
                'routes' => [
                    'test1' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/testing-url-1'
                        ]
                    ],
                    'test2' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/testing-url-2'
                        ]
                    ]
                ],
            ],
        ];
    }

    public function getCliConfig(): array
    {
        // Loaded commands files
        /*$commands = \LmConsole\Model\ModuleCommandLoader::getCommands();
        if (!$commands) {
            return [];
        }

        // Get list of all modules commandes
        // Retrieve COMMAND [arguments] list
        $commandsList = [];
        foreach ($commands as $command) {
            $key = ($command)::getDefaultName() . ' ' . ($command)::getDefaultArguments();
            $commandsList[ $key ] = ($command);
        }

        return [
            'commands' => $commandsList
        ];*/

        return [
            'commands' => [
                'debug:routes [module]' => Command\DebugRoutesCommand::class,
            ]
        ];
    }
}
