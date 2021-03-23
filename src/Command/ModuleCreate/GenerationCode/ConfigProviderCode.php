<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

use Laminas\Code\Generator\{FileGenerator, ClassGenerator, MethodGenerator, DocBlockGenerator};
use Laminas\Code\Reflection\ClassReflection;
use LmConsole\Command\ModuleCreate\GenerationModel;

class ConfigProviderCode extends AbstractCode
{
    use ConfigTrait;

    const FILENAME = 'ConfigProvider.php';

    /**
     * @param int $option 
     *  0: Straight inside Module.php
     *  1: Used with modules.config.php
     *  2: Can be provided with ConfigProvider::__invoke()
     */
    protected function generateCode(string $className, string $namespace): string
    {
        // METHODS //
        $invokeMethod = new MethodGenerator(
            '__invoke',                  // name
            [],                  // parameters
            0,                 // visibility
'return [
    "router" => $this->getRouteConfig(), 
    "view_manager" => $this->getViewManagerConfig(),

    // Remove if use DI
    "controller" => $this->getControllerConfig()
];'  // body
        );

        $routerMethod = new MethodGenerator(
            'getRouteConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getRoutes($namespace) . "\n];"  // body
        );

        $viewMethod = new MethodGenerator(
            'getViewManagerConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getView() . "\n];"  // body
        );

        $controllerMethod = new MethodGenerator(
            'getControllerConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getController() . "\n];"  // body
        );

        // with getServiceConfig and getConfig
        $methods = [
            $invokeMethod,
            $routerMethod,
            $viewMethod,
            $controllerMethod->setDocBlock('Remove if use DI')
        ];

        // CLASS //
        $class = new ClassGenerator(
            $className,  // name
            $namespace,
            0, // flags
            '', //ext
            [
                'ConfigProviderInterface',
                'RouteProviderInterface',
                'ServiceProviderInterface'
            ], // interfaces
            [], //properties
            $methods,
            new DocBlockGenerator(
                $className, //short
                "Can be implemented with common features:
\t\tLaminas\ModuleManager\Feature\ControllerPluginProviderInterface
\t\tLaminas\ModuleManager\Feature\ViewHelperInterface\nand others..." //long desc
                //tags
            )
        );

        $class->addUse('Laminas\ModuleManager\Feature\ConfigProviderInterface');
        $class->addUse('Laminas\ModuleManager\Feature\RouteProviderInterface');
        $class->addUse('Laminas\ModuleManager\Feature\ServiceProviderInterface');
        $class->addUse('Laminas\Router\Http\Literal');
        $class->addUse('Laminas\ServiceManager\Factory\InvokableFactory');

        // FILE //
        $file = new FileGenerator([
            'classes' => [
                $class
            ]
        ]);
        $generated = $file->generate();

        /**
         * fix \extendsClassName
         */
        $generated = str_replace(' \\ConfigProviderInterface', ' ConfigProviderInterface', $generated);
        $generated = str_replace(' \\RouteProviderInterface', ' RouteProviderInterface', $generated);
        $generated = str_replace(' \\ServiceProviderInterface', ' ServiceProviderInterface', $generated);
        return $generated;
    }
}
