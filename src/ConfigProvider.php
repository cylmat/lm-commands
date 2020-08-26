<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole;

use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),

            'controllers' => [
                'factories' => [
                    TestController\TestController::class => InvokableFactory::class
                ],
            ],

            // For unit testing framework
            'view_manager' => [
                'exception_template' => 'error/index',
                'template_map'       => [
                    'layout/layout' => __DIR__ . '/error.phtml',
                    'error/404'     => __DIR__ . '/error.phtml',
                    'error/index'   => __DIR__ . '/error.phtml',
                ],
            ],
            'router'       => [
                'routes' => [
                    'test1' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route' => '/testing-url-1[/controller:]',
                        ],
                    ],
                    'test2' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route' => '/testing-url-2',
                        ],
                    ],
                    'test3' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/', //'/album-tuto[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9]*',
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => TestController\TestController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }

    public function getCliConfig(): array
    {
        $commands = null;
        if (! isset($GLOBALS[Model\GlobalConfigRetriever::GLOBAL_REDUNDANCE_AVOIDER])) {
            $commands = Model\ModuleCommandLoader::getModulesCommands();
        }

        if (! $commands) {
            return [];
        }

        // Get list of all modules commandes
        // Retrieve COMMAND [arguments] list
        $commandsList = [];
        foreach ($commands as $command) {
            $key                  = $command::getDefaultName(); 
            $commandsList[ $key ] = $command;
        }

        return [
            'commands' => $commandsList
        ];
    }
}
