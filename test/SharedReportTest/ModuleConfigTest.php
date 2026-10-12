<?php

declare(strict_types=1);

namespace SharedReportTest;

use PHPUnit\Framework\TestCase;

/**
 * Module Configuration Tests
 *
 * Validates that the SharedReport module is properly configured
 * and loads required dependencies for mot-logger v3.3.1.
 *
 * @package SharedReportTest
 */
class ModuleConfigTest extends TestCase
{
    private array $config;

    public function setUp(): void
    {
        $this->config = include __DIR__ . '/../../config/application.config.php';
    }

    public function testApplicationConfigHasModules(): void
    {
        $this->assertArrayHasKey('modules', $this->config, 'modules key not found in application config');
    }

    public function testModulesConfigIsArray(): void
    {
        $this->assertIsArray($this->config['modules'], 'modules should be an array');
    }

    public function testDvsaLoggerModuleIsLoaded(): void
    {
        $this->assertContains(
            'DvsaLogger',
            $this->config['modules'],
            'DvsaLogger module is required for mot-logger integration but not loaded'
        );
    }

    public function testSharedReportModuleIsLoaded(): void
    {
        $this->assertContains(
            'SharedReport',
            $this->config['modules'],
            'SharedReport module should be loaded'
        );
    }

    public function testDvsaLoggerLoadedBeforeSharedReport(): void
    {
        $modules = $this->config['modules'];
        $dvsaLoggerPos = array_search('DvsaLogger', $modules);
        $sharedReportPos = array_search('SharedReport', $modules);

        $this->assertLessThan(
            $sharedReportPos,
            $dvsaLoggerPos,
            'DvsaLogger module should be loaded before SharedReport to ensure logger availability'
        );
    }

    public function testModuleListenerOptionsExist(): void
    {
        $this->assertArrayHasKey(
            'module_listener_options',
            $this->config,
            'module_listener_options not found in application config'
        );
    }

    public function testModulePathsConfigured(): void
    {
        $this->assertArrayHasKey(
            'module_paths',
            $this->config['module_listener_options'],
            'module_paths not configured'
        );

        $this->assertIsArray($this->config['module_listener_options']['module_paths']);
        $this->assertNotEmpty($this->config['module_listener_options']['module_paths']);
    }

    public function testConfigGlobPathsConfigured(): void
    {
        $this->assertArrayHasKey(
            'config_glob_paths',
            $this->config['module_listener_options'],
            'config_glob_paths not configured'
        );

        $this->assertIsArray($this->config['module_listener_options']['config_glob_paths']);
        $this->assertNotEmpty($this->config['module_listener_options']['config_glob_paths']);
    }
}
