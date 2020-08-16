<?php

namespace LmConsole;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),
            
            'view_manager' => [
                'display_not_found_reason' => true,
                'display_exceptions'       => true,
                //'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/404',
                'exception_template'       => 'error/index',
                'template_map' => [
                    'application/index/index' => __DIR__ . '/../view/index/index.phtml',
                    'layout/layout'           => __DIR__ . '/error.phtml',
                    'error/404'               => __DIR__ . '/error.phtml',//'/../view/error/404.phtml',
                    'error/index'             => __DIR__ . '/error.phtml',
                ],
                'template_path_stack' => [
                    __DIR__ . '/',
                ]
            ]
        ];
    }

    public function getCliConfig(): array
    {
        return [
            'commands' => [
                'debug:routes [module]' => Command\DebugRoutesCommand::class,
            ],
        ];
    }
}
