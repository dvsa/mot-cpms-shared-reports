<?php

namespace SharedReportTest;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package SharedReportTest
 */
class Module
{
    /**
     * Bootstrap event
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $application         = $event->getApplication();
        $eventManager        = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../test.global.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
