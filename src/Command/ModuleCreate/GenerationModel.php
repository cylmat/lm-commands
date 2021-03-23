<?php

namespace LmConsole\Command\ModuleCreate;

class GenerationModel
{
    const OPTION_MODULE     = 0;
    const OPTION_CONFIG     = 1;
    const OPTION_PROVIDER   = 2;

    /**
     * 
     * @param string $modules_path
     * @param InputInterface $input
     * 
     * @return bool
     */
    public function createFiles(string $modules_path, string $new_module_name, int $option): bool
    {
        $root_dir = $modules_path . DIRECTORY_SEPARATOR .  $new_module_name . '/' ;
        $src_dir = $root_dir . 'src/' ;
        $namespace = $new_module_name;

        // Module created in options 'switch'
        if ($option !== self::OPTION_MODULE) {
            $cModule = $this->createCodeFile('Module', $src_dir, $namespace, $option);
        }

        // IndexController.php
        $cIndexController = $this->createCodeFile('IndexController', $src_dir . 'Controller', $namespace . "\\" . 'Controller');

        // OPTIONS //
        switch ($option) {
            case self::OPTION_MODULE:
                //Module.php
                $cModule = $this->createCodeFile('Module', $src_dir, $namespace, $option);
                break;

            case self::OPTION_CONFIG:
                //module.config.php
                $cModuleConfig = $this->createCodeFile('ModuleConfig', $root_dir . 'config', $namespace);
                break;
            
            case self::OPTION_PROVIDER:
                //module.config.php
                $cProvider = $this->createCodeFile('ConfigProvider', $src_dir, $namespace);
                break;
        }
        

        if ($cModule && $cIndexController) {
            return true;
        }
        return false;
    }

    /* protected */

    /**
     * Call the <codefilename>Code
     * 
     * @param string $pathDir
     * 
     * @return bool
     * @throws \Exception
     */
    protected function createCodeFile(string $codeFilename, string $destinationPath, string $namespace, $params=null): bool
    {
        $codeClass =  __NAMESPACE__ . "\\GenerationCode\\" . $codeFilename . 'Code';
        if (!class_exists($codeClass)) {
            throw new \Exception("Le fichier de génération $codeClass n'existe pas.\n");
        }
        
        if (!file_exists($destinationPath)) {
            throw new \Exception("Le répertoire de destination $destinationPath n'existe pas.\n");
        }

        // Set destination file
        $generate = [
            'classname' => $codeFilename,
            'namespace' => $namespace
        ];
        if (!(new $codeClass($params))->create($destinationPath, $generate)) {
            throw new \Exception("Impossible de générer le code de $codeClass .\n");
        } 
        return true;
    }
}