<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LmConsole\TestController;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class TestController extends AbstractActionController
{
    public function indexAction()
    {
        $this->getEventManager()->attach('test.event', new class {function __invoke($e){
            $event = $e->getName();
            $params = $e->getParams();
            
            printf(
                'SAMPLE LISTENER: Handled event "%s", with parameters %s<br/>',
                $event,
                json_encode($params)
            );
        }}, 1234567);
    }
}
