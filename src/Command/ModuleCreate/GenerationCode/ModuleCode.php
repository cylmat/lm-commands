<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

use Laminas\Code\Generator\{FileGenerator, ClassGenerator, MethodGenerator, DocBlockGenerator};
use Laminas\Code\Reflection\ClassReflection;
use LmConsole\Command\ModuleCreate\GenerationModel;

class ModuleCode extends AbstractCode
{
    use ConfigTrait;

    const FILENAME = 'Module.php';

    protected $option;

    public function __construct($params=null) {
        $this->option = $params;
    }

    /**
     * @param int $option 
     *  0: Straight inside Module.php
     *  1: Used with modules.config.php
     *  2: Can be provided with ConfigProvider::__invoke()
     */
    protected function generateCode(string $className, string $namespace): string
    {
        // METHODS //
        $methods = [];

        // Configuration return depends of input option
        $configGenerator = new MethodGenerator(
            'getConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            ""  // body
        );
        switch ($this->option) {
            case GenerationModel::OPTION_MODULE: // Module.php
                $configGenerator->setBody(
'return [
    "router" => $this->getRouteConfig(),
    "view_manager" => $this->getViewManagerConfig(),

    // Remove if you use DI
    "controller" => $this->getControllerConfig()
];'
                );
                $methods = $this->getModuleCode($namespace);
                array_unshift($methods, $configGenerator);
                break;

            case GenerationModel::OPTION_CONFIG: // module.config.php
                $configGenerator->setBody("return include \"../config/module.config.php\";");
                $methods[] = $configGenerator;
                break;

            case GenerationModel::OPTION_PROVIDER: //ConfigProvider
                $configGenerator->setBody("return (new ConfigProvider)();");
                $methods[] = $configGenerator;
                break;
        }

        // CLASS //
        $interfaces = [
            'ConfigProviderInterface',
            'ServiceProviderInterface'
        ]; // interfaces

        // for MODULE.php config
        if ($this->option === GenerationModel::OPTION_MODULE) {
            $interfaces[] = 'RouteProviderInterface';
        }

        $class = new ClassGenerator(
            $className,  // name
            $namespace,
            0, // flags
            '', //ext
            $interfaces, // interfaces
            [], //properties
            $methods,
            new DocBlockGenerator(
                $className, //short
                "Can be implemented with common features:
\tLaminas\ModuleManager\Feature\ConfigProviderInterface\n" . 
($this->option === GenerationModel::OPTION_MODULE ? '' : "\tLaminas\ModuleManager\Feature\RouteProviderInterface\n") . //remove from comments for Module.php config
"\tLaminas\ModuleManager\Feature\ControllerPluginProviderInterface
\tLaminas\ModuleManager\Feature\ViewHelperInterface\nand others..." //long desc
                //tags
            )
        );
        $class->addUse('Laminas\ModuleManager\Feature\ConfigProviderInterface');
        $class->addUse('Laminas\ModuleManager\Feature\ServiceProviderInterface');

        // for MODULE.php config
        if ($this->option === GenerationModel::OPTION_MODULE) {
            $class->addUse('Laminas\ModuleManager\Feature\RouteProviderInterface');
            $class->addUse('Laminas\Router\Http\Literal');
            $class->addUse('Laminas\ServiceManager\Factory\InvokableFactory');
        }

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

    /* protected */

    protected function getModuleCode($namespace)
    {
        $methdso = [];
        $methods[] = new MethodGenerator(
            'getRouteConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getRoutes($namespace) . "];"  // body
        );
        $methods[] = new MethodGenerator(
            'getViewManagerConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getView() . "];"  // body
        );

        // Service config
        $methods[] = new MethodGenerator(
            'getServiceConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n];"  // body
        );
        $methods[] = (new MethodGenerator(
            'getControllerConfig',                  // name
            [],                  // parameters
            0,                 // visibility
            "return [\n\t" . ConfigTrait::getController() . "];"  // body
        ))->setDocBlock(new DocBlockGenerator('Remove if you use DI'));

        return $methods;
    }
}
