<?php

declare(strict_types=1);

namespace SharedReportTest;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package SharedReportTest
 */
final class Module
{
    /**
     * Bootstrap event
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event): void
    {
        $application         = $event->getApplication();
        $eventManager        = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * @return mixed
     */
    public function getConfig(): mixed
    {
        return include __DIR__ . '/../test.global.php';
    }

    /**
     * @return array<string, mixed>
     */
    public function getAutoloaderConfig(): array
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
