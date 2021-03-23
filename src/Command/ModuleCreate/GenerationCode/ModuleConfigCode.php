<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

use Laminas\Code\Generator\{DocBlockGenerator, FileGenerator};

class ModuleConfigCode extends AbstractCode
{
    use ConfigTrait;

    const FILENAME = 'module.config.php';

    protected function generateCode(string $className, string $namespace, int $option=2): string
    {
        $controllerCode = ConfigTrait::getController();
        $routesCode = ConfigTrait::getRoutes($namespace);
        $viewCode = ConfigTrait::getView();

        $body = <<<B
return [
'router' => [
    $routesCode
],
'view_manager' => [
    $viewCode
],

/*
* Remove this section if you use DI
*/
'controllers' => [
    $controllerCode
]
];
B;

        // FILE //
        $file = new FileGenerator;
        $file->setDocBlock(new DocBlockGenerator("Module $namespace"));
        $file->setUse("Laminas\Router\Http\Literal");
        $file->setUse("Laminas\Router\Http\Literal");
        $file->setUse("Laminas\Router\Http\Literal");
        $file->setUse("Laminas\ServiceManager\Factory\InvokableFactory");
        $file->setBody($body);
        $generated = $file->generate();
        return $generated;
    }
}
