<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

use Laminas\Code\Generator\{FileGenerator, ClassGenerator, MethodGenerator, DocBlockGenerator};
use Laminas\Code\DeclareStatement;
use Laminas\Code\Reflection\ClassReflection;

class IndexControllerCode extends AbstractCode
{
    const FILENAME = 'IndexController.php';

    /**
     * 
     */
    protected function generateCode(string $className, string $namespace): string
    {
        $properties = [
        ]; // properties;

        $methods = [
            new MethodGenerator(
                'indexAction',                  // name
                [],                  // parameters
                0,                 // visibility
                "return new ViewModel([\n]);"  // body
            )
        ];

        $class = new ClassGenerator(
            $className,  // name
            $namespace,
            0, // flags
            'AbstractActionController', //ext
            null, // interfaces
            $properties,
            $methods,
            new DocBlockGenerator(
                $className//short
                //long
                //tags
            )
        );
        $class->addUse('Laminas\Mvc\Controller\AbstractActionController');
        $class->addUse('Laminas\View\Model\ViewModel');

        $file = new FileGenerator([
            'classes' => [
                $class
            ]
        ]);

        $generated = $file->generate();

        /**
         * fix \extendsClassName
         */
        $generated = str_replace(' \\AbstractActionController', ' AbstractActionController', $generated);
        return $generated;
    }
}
