<?php

namespace LmConsole\Command\ModuleCreate;

class DirectoryModel
{
    
    protected static $directories = [
        'config',
        'src',
        'src/Controller',
        'test',
        'view'
    ];

    /**
     * @param string $modules_path
     * @param InputInterface $input
     * 
     * @return bool
     * @throws \E_USER_ERROR
     */
    public function createDirectories(string $modules_path, string $new_module_name): bool
    {
        //create new module
        $new_module_path = 
            $modules_path . 
            \DIRECTORY_SEPARATOR .
            $new_module_name .
            \DIRECTORY_SEPARATOR;

        if (!file_exists($new_module_path)) {
            mkdir($new_module_path);
        } else {
            //throw new \Exception("Le répertoire {$new_module_path} existe déjà.\n");
            //return false;
        }

        //create new module subdirs
        
        $config_dir = $new_module_path . 'config';
        $this->createDirectory($config_dir);
        
        $src_dir = $new_module_path . 'src' . \DIRECTORY_SEPARATOR;
        $this->createDirectory($src_dir);

        $src_ctrl_dir = $src_dir . 'Controller';
        $this->createDirectory($src_ctrl_dir);
        
        $test_dir = $new_module_path . 'test';
        $this->createDirectory($test_dir);
        
        $view_dir = $new_module_path . 'view';
        $this->createDirectory($view_dir);

        return true;
    }

    /**
     * Get modules dir 
     * 
     * @throws Exception If directory doesn't exists
     */
    public function validateModulesDirectory(string $modules_dir): void
    {
        $dir = getcwd() . DIRECTORY_SEPARATOR;

        //valide modules directory
        $modules_path = $dir . $modules_dir;

        if (!file_exists($modules_path)) {
            throw new \Exception("Le répertoire de modules {$modules_path} n'existe pas.\n");
        }
    }

    /* protected */

    /**
     * @param string $pathDir
     * 
     * @return bool
     */
    protected function createDirectory(string $pathDir): bool
    {
        if (!file_exists($pathDir)) {
            return mkdir($pathDir); 
        }
        return false;
    }
}