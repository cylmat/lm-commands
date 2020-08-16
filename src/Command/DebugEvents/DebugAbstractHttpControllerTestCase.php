<?php

namespace LmConsole\Model\DebugEvents;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

$app = init($appConfig);
$sm = $app->getServiceManager(); 

$listenersFromAppConfig     = isset($appConfig['listeners']) ? $appConfig['listeners'] : [];
$config                     = $sm->get('config');
$listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];
$listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));


$app->bootstrap($listeners);
$app->run();

//$app->getServiceManager()->debug();
$app->getEventManager()->debug();

(new dede)->run();

//dm($abCtrl->getResponse());


/*$container = $app->getServiceManager(); 
$shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
$em = new EventManager($shared);*/
//dm($sm);
/*$app->setEventManager($em);
$app->run();*/

class DebugAbstractHttpControllerTestCase extends AbstractHttpControllerTestCase
{
    public function setUp() : void
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    function initApplication($configuration = [])
    {
        // Prepare the service manager
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new \Laminas\Mvc\Service\ServiceManagerConfig($smConfig);

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
    }

    /**
     * Get the application object
     * @return \Laminas\Mvc\ApplicationInterface
     */
    public function getApplication()
    {
        if ($this->application) {
            return $this->application;
        }
        $appConfig = $this->applicationConfig;
        Console::overrideIsConsole($this->getUseConsoleRequest());
        $this->application = Application::init($appConfig);

        $events = $this->application->getEventManager();
        $this->application->getServiceManager()->get('SendResponseListener')->detach($events);

        return $this->application;
    }

    public function runApplication()
    {
        $abCtrl->setUp();
        $res = $abCtrl->dispatch('/');
    }
}