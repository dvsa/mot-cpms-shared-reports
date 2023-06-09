<?php

namespace SharedReport;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package CpmsReport
 * @author  Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Module
{
    /**
     * Prepares the view layer
     *
     * @param  $event
     *
     * @return void
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
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
