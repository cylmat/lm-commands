<?php

/**
 * If you need an environment-specific system or application configuration,
 * there is an example in the documentation
 * @see https://docs.laminas.dev/tutorials/advanced-config/#environment-specific-system-configuration
 * @see https://docs.laminas.dev/tutorials/advanced-config/#environment-specific-application-configuration
 */
return [
    'modules' => ['Laminas\Router','LmConsole'],
    'module_listener_options' => [
        'use_laminas_loader' => false,
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'
        ]
    ]
];
