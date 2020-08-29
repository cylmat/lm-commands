<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsoleTest;

use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
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
}
