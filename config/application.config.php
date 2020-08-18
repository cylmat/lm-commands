<?php

use Interop\Container\ContainerInterface;
use LmConsole\Command\DebugEventsModel\EventDebuggerManager;

/**
 * If you need an environment-specific system or application configuration,
 * there is an example in the documentation
 * @see https://docs.laminas.dev/tutorials/advanced-config/#environment-specific-system-configuration
 * @see https://docs.laminas.dev/tutorials/advanced-config/#environment-specific-application-configuration
 */
return [
    'modules' => [
        'Laminas\Router',
        'LmConsole'
    ],

    'module_listener_options' => [
        'use_laminas_loader' => false,
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'
        ]
    ],
    'service_manager' =>  [
        'factories'  => [
            'EventManager' => function (ContainerInterface $container, $name, array $options = null) {
                $shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
                return new EventDebuggerManager($shared);
            }
           // 'ServiceListener'         => ServiceListenerFactory::class,
        ],
    ]
];
