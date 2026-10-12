<?php

declare(strict_types=1);

namespace SharedReportTest\Config;

use PHPUnit\Framework\TestCase;

/**
 * Logger Configuration Tests
 *
 * Validates that the mot-logger v3.3.1 configuration is properly set up
 * for the CPMS reports module.
 *
 * @package SharedReportTest\Config
 */
class LoggerConfigTest extends TestCase
{
    private array $config;

    public function setUp(): void
    {
        $this->config = include __DIR__ . '/../../../config/autoload/config.global.php';
    }

    public function testMotLoggerConfigExists(): void
    {
        $this->assertArrayHasKey('mot_logger', $this->config, 'mot_logger configuration not found');
    }

    public function testMotLoggerConfigIsArray(): void
    {
        $this->assertIsArray($this->config['mot_logger'], 'mot_logger configuration should be an array');
    }

    public function testLoggersConfigExists(): void
    {
        $this->assertArrayHasKey(
            'loggers',
            $this->config['mot_logger'],
            'loggers key not found in mot_logger config',
        );
    }

    public function testCpmsReportsChannelExists(): void
    {
        $this->assertArrayHasKey(
            'cpms-reports',
            $this->config['mot_logger']['loggers'],
            'cpms-reports channel not configured'
        );
    }

    public function testCpmsReportsChannelConfig(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];

        $this->assertArrayHasKey('channel', $cpmsReportsConfig);
        $this->assertEquals('cpms-reports', $cpmsReportsConfig['channel']);
    }

    public function testCpmsReportsWritersConfigExists(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];

        $this->assertArrayHasKey(
            'writers',
            $cpmsReportsConfig,
            'writers key not found in cpms-reports config',
        );
        $this->assertIsArray($cpmsReportsConfig['writers']);
        $this->assertNotEmpty($cpmsReportsConfig['writers'], 'No writers configured for cpms-reports channel');
    }

    public function testFirstWriterType(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertArrayHasKey('type', $writer);
        $this->assertEquals('stream', $writer['type'], 'Writer type should be stream');
    }

    public function testWriterFormatterIsJson(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertArrayHasKey('formatter', $writer);
        $this->assertEquals(
            'json',
            $writer['formatter'],
            'Writer formatter should be json',
        );
    }

    public function testWriterLogLevel(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertArrayHasKey('level', $writer);
        $this->assertEquals(
            'info',
            $writer['level'],
            'Log level should be info',
        );
    }

    public function testWriterEnabled(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertArrayHasKey('enabled', $writer);
        $this->assertTrue($writer['enabled'], 'Writer should be enabled');
    }

    public function testWriterPathExists(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertArrayHasKey('path', $writer, 'Writer path not configured');
        $this->assertNotEmpty($writer['path'], 'Writer path should not be empty');
    }

    public function testWriterPathContainsDvsaDirectory(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertStringContainsString(
            '/var/log/dvsa/',
            $writer['path'],
            'Log path should be in /var/log/dvsa/ directory',
        );
    }

    public function testWriterPathContainsDynamicDate(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        // Path contains evaluated date (YYYY-MM-DD format) for daily log rotation
        $this->assertMatchesRegularExpression(
            '/\d{4}-\d{2}-\d{2}/',
            $writer['path'],
            'Log path should contain date in YYYY-MM-DD format for daily rotation'
        );
    }

    public function testWriterPathContainsLogFileName(): void
    {
        $cpmsReportsConfig = $this->config['mot_logger']['loggers']['cpms-reports'];
        $writer = reset($cpmsReportsConfig['writers']);

        $this->assertStringContainsString(
            'cpms-reports.log',
            $writer['path'],
            'Log file name should be cpms-reports.log'
        );
    }
}
