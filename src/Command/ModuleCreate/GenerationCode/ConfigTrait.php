<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

trait ConfigTrait
{
    public static function getController()
    {
        return 
    "'factories' => [
        Controller\IndexController::class => InvokableFactory::class
    ]";
    }

    public static function getRoutes(string $namespace)
    {
        return 
    "'routes' => [
        '$namespace' => [
            'type'    => Literal::class,
            'options' => [
                'route'    => '/$namespace',
                'defaults' => [
                    'controller' => Controller\IndexController::class,
                    'action'     => 'index',
                ],
            ],
        ]
    ]";
    }

    public static function getView()
    {
        return
    "'template_path_stack' => [
        __DIR__ . '/../view',
    ]";
    }
}