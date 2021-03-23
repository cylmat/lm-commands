<?php

declare(strict_types=1);

namespace LmConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LmConsole\Command\ModuleCreate\AbstractFactory;

class ModuleCreateCommand extends AbstractCommand
{
    protected static $defaultName = 'module:create';

    /**
     * @var $modules_dir
     */
    protected static $modules_dir = 'generated_modules';


    /**
     * Config
     */
    protected function configure()
    {
        $this
            ->addArgument('module_name', InputArgument::REQUIRED, 'The name of new module to create.');

        $this
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Use 0) Module.php, 1) modules.config.php or 2) ConfigProvider.php.', 2);

        $this
            ->setDescription('todo*** Create a bootstrap module')
            ->setHelp('This command allows you create a new module with bootstrap files.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return int
     */
    function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        AbstractFactory::DirectoryModel()->validateModulesDirectory(self::$modules_dir); // Throw Exception

        if (!AbstractFactory::DirectoryModel()->createDirectories(self::$modules_dir, $input->getArgument('module_name'))) {
            return Command::FAILURE;
        }

        $option = (int)$input->getOption('config'); 
        if (!AbstractFactory::GenerationModel()->createFiles(self::$modules_dir, $input->getArgument('module_name'), $option)) {
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }

    
}