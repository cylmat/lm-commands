<?php

namespace LmConsole\Command;

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class DebugAbstractHttpControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp() : void
    {
        $configOverrides = [
           /* 'service_manager' => [
                
            ]*/
           /*'view_manager' => [
                'display_not_found_reason' => false,
                'display_exceptions'       => false,
                //'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/404',
                'exception_template'       => 'error/index',
                'template_map' => [
                    //'application/index/index' => __DIR__ . '/../view/index/index.phtml',
                    'layout/layout'           => __DIR__ . '/alpha.phtml',
                    'error/404'               => __DIR__ . '/alpha.phtml',//'/../view/error/404.phtml',
                    'error/index'             => __DIR__ . '/alpha.phtml',
                ],
                'template_path_stack' => [
                    __DIR__ . '/',
                ]
            ]*/
        ];

        $this->setApplicationConfig(array_merge(
            include __DIR__ . '/../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    /*public function initApplication($configuration = [])
    {
        // Prepare the service manager
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new ServiceManagerConfig($smConfig);

        $serviceManager = new DebugServiceManager;
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);
        
        // Load modules
        $serviceManager->get('ModuleManager')->loadModules();

        // Prepare list of listeners to bootstrap
        $listenersFromAppConfig     = isset($configuration['listeners']) ? $configuration['listeners'] : [];
        $config                     = $serviceManager->get('config');
        $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];

        $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));

        return $serviceManager->get('Application');
    }*/

    /**
     * Get the application object
     * @return \Laminas\Mvc\ApplicationInterface
     */
    /*public function getApplication()
    {
        if ($this->application) {
            return $this->application;
        }
        $appConfig = $this->applicationConfig;
        //Console::overrideIsConsole($this->getUseConsoleRequest());
        $this->application = \Laminas\Mvc\Application::init($appConfig);

        $events = $this->application->getEventManager();
        $this->application->getServiceManager()->get('SendResponseListener')->detach($events);

        return $this->application;
    }*/

    public function testRunApplication()
    {
        $this->dispatch('/testing-url-1');
    }
}

/*$app = init($appConfig);
$sm = $app->getServiceManager(); 

$listenersFromAppConfig     = isset($appConfig['listeners']) ? $appConfig['listeners'] : [];
$config                     = $sm->get('config');
$listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];
$listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));


$app->bootstrap($listeners);
$app->run();

//$app->getServiceManager()->debug();
$app->getEventManager()->debug();

(new dede)->run();*/

//dm($abCtrl->getResponse());


/*$container = $app->getServiceManager(); 
$shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
$em = new EventManager($shared);*/
//dm($sm);
/*$app->setEventManager($em);
$app->run();*/