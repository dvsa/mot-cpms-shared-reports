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
final class ModuleConfigTest extends TestCase
{
    /**
     * @var array<string, mixed> $config
     */
    private array $config = [];

    #[\Override]
    public function setUp(): void
    {
        $config = include __DIR__ . '/../../config/application.config.php';
        if (!is_array($config)) {
            $this->fail('application config did not return an array');
        }
        /** @var array<string,mixed> $config */
        $this->config = $config;
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
        $modules = isset($this->config['modules']) && is_array($this->config['modules']) ? $this->config['modules'] : [];

        $this->assertContains(
            'DvsaLogger',
            $modules,
            'DvsaLogger module is required for mot-logger integration but not loaded'
        );
    }

    public function testSharedReportModuleIsLoaded(): void
    {
        $modules = isset($this->config['modules']) && is_array($this->config['modules']) ? $this->config['modules'] : [];

        $this->assertContains(
            'SharedReport',
            $modules,
            'SharedReport module should be loaded'
        );
    }

    public function testDvsaLoggerLoadedBeforeSharedReport(): void
    {
        $modules = isset($this->config['modules']) && is_array($this->config['modules']) ? $this->config['modules'] : [];
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
        $this->assertNotEmpty($this->getModuleListenerOptions());
    }

    public function testModulePathsConfigured(): void
    {
        $options = $this->getModuleListenerOptions();

        $this->assertArrayHasKey(
            'module_paths',
            $options,
            'module_paths not configured'
        );

        $this->assertIsArray($options['module_paths']);
        $this->assertNotEmpty($options['module_paths']);
    }

    public function testConfigGlobPathsConfigured(): void
    {
        $options = $this->getModuleListenerOptions();

        $this->assertArrayHasKey(
            'config_glob_paths',
            $options,
            'config_glob_paths not configured'
        );

        $this->assertIsArray($options['config_glob_paths']);
        $this->assertNotEmpty($options['config_glob_paths']);
    }

    /**
     * Helper to return module_listener_options as an asserted array
     *
     * @return array<string,mixed>
     */
    private function getModuleListenerOptions(): array
    {
        $this->assertArrayHasKey(
            'module_listener_options',
            $this->config,
            'module_listener_options not found in application config'
        );

        $options = $this->config['module_listener_options'] ?? null;
        $this->assertIsArray($options, 'module_listener_options must be an array');

        /** @var array<string,mixed> $options */
        return $options;
    }
}
