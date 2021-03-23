<?php

namespace LmConsole\Command\ModuleCreate\GenerationCode;

use Laminas\Code\Generator\{FileGenerator, ClassGenerator, MethodGenerator, DocBlockGenerator};
use Laminas\Code\DeclareStatement;
use Laminas\Code\Reflection\ClassReflection;

abstract class AbstractCode 
{
    /**
     * @param string $filePath
     * 
     * $generate must contains [
     *      'classname'=>,
     *      'namespace'=>
     * ]
     * @param array $generate
     * 
     * @return bool
     */
    public function create(string $fileDir, array $generate): bool
    {
        $code = $this->generateCode($generate['classname'], $generate['namespace']);

        if (!defined(static::class . "::FILENAME")) {
            throw new \LogicException("Constante FILENAME not defined " . static::class);
        }

        $filePath = $fileDir . DIRECTORY_SEPARATOR . static::FILENAME;
        return $this->writeInFile($filePath, $code);
    }

    /**
     * Generation of file code
     */
    protected abstract function generateCode(string $className, string $namespace): string;

    protected function writeInFile(string $filePath, string $code): bool
    {
        return file_put_contents($filePath, $code);
    }
}
