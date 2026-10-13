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
final class LoggerConfigTest extends TestCase
{
    /**
     * @var array<string, mixed> $config
     */
    private array $config = [];

    #[\Override]
    public function setUp(): void
    {
        $config = include __DIR__ . '/../../../config/autoload/config.global.php';
        if (!is_array($config)) {
            $this->fail('Logger config file did not return an array');
        }
        /** @var array<string, mixed> $config */
        $this->config = $config;
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
        $motLogger = $this->getMotLoggerConfig();

        $this->assertArrayHasKey(
            'loggers',
            $motLogger,
            'loggers key not found in mot_logger config',
        );
    }

    public function testCpmsReportsChannelExists(): void
    {
        $this->assertNotEmpty($this->getCpmsReportsConfig());
    }

    public function testCpmsReportsChannelConfig(): void
    {
        $cpmsReportsConfig = $this->getCpmsReportsConfig();

        $this->assertArrayHasKey('channel', $cpmsReportsConfig);
        $this->assertEquals('cpms-reports', $cpmsReportsConfig['channel']);
    }

    public function testCpmsReportsWritersConfigExists(): void
    {
        $cpmsReportsConfig = $this->getCpmsReportsConfig();

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
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('type', $writer);
        $this->assertEquals('stream', $writer['type'], 'Writer type should be stream');
    }

    public function testWriterFormatterIsJson(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('formatter', $writer);
        $this->assertEquals(
            'json',
            $writer['formatter'],
            'Writer formatter should be json',
        );
    }

    public function testWriterLogLevel(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('level', $writer);
        $this->assertEquals(
            'info',
            $writer['level'],
            'Log level should be info',
        );
    }

    public function testWriterEnabled(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('enabled', $writer);
        $this->assertTrue($writer['enabled'], 'Writer should be enabled');
    }

    public function testWriterPathExists(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('path', $writer, 'Writer path not configured');
        $this->assertNotEmpty($writer['path'], 'Writer path should not be empty');
    }

    public function testWriterPathContainsDvsaDirectory(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('path', $writer);
        $this->assertIsString($writer['path']);

        $this->assertStringContainsString(
            '/var/log/dvsa/',
            $writer['path'],
            'Log path should be in /var/log/dvsa/ directory',
        );
    }

    public function testWriterPathContainsDynamicDate(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('path', $writer);
        $this->assertIsString($writer['path']);

        // Path contains evaluated date (YYYY-MM-DD format) for daily log rotation
        $this->assertMatchesRegularExpression(
            '/\\d{4}-\\d{2}-\\d{2}/',
            $writer['path'],
            'Log path should contain date in YYYY-MM-DD format for daily rotation'
        );
    }

    public function testWriterPathContainsLogFileName(): void
    {
        $writer = $this->getFirstWriter();

        $this->assertArrayHasKey('path', $writer);
        $this->assertIsString($writer['path']);

        $this->assertStringContainsString(
            'cpms-reports.log',
            $writer['path'],
            'Log file name should be cpms-reports.log'
        );
    }

    /**
     * Helper returning the first writer after asserting the config shape.
     *
     * @return array<string,mixed>
     */
    private function getFirstWriter(): array
    {
        $typed = $this->getCpmsReportsConfig();
        $writers = $typed['writers'];
        $this->assertNotEmpty($writers, 'No writers configured for cpms-reports channel');

        /** @var array<string,mixed> $first */
        $first = $writers[0];

        return $first;
    }

    /**
     * Return the mot_logger configuration node after asserting it exists and is an array.
     *
     * @return array<string,mixed>
     */
    private function getMotLoggerConfig(): array
    {
        $this->assertArrayHasKey('mot_logger', $this->config, 'mot_logger configuration not found');
        $this->assertIsArray($this->config['mot_logger']);

        /** @var array<string,mixed> $cfg */
        $cfg = $this->config['mot_logger'];

        return $cfg;
    }

    /**
     * Helper that asserts the expected logger config shape and returns the cpms-reports config.
     *
     * @return array{channel:string, writers:list<array<string,mixed>>}
     */
    private function getCpmsReportsConfig(): array
    {
        $this->assertArrayHasKey('mot_logger', $this->config, 'mot_logger configuration not found');
        $this->assertIsArray($this->config['mot_logger']);
        $this->assertArrayHasKey('loggers', $this->config['mot_logger'], 'loggers key not found in mot_logger config');
        $this->assertIsArray($this->config['mot_logger']['loggers']);
        $this->assertArrayHasKey('cpms-reports', $this->config['mot_logger']['loggers'], 'cpms-reports channel not configured');
        $this->assertIsArray($this->config['mot_logger']['loggers']['cpms-reports']);

        /** @var array<string,mixed> $cfg */
        $cfg = $this->config['mot_logger']['loggers']['cpms-reports'];

        $channel = isset($cfg['channel']) && is_string($cfg['channel']) ? $cfg['channel'] : '';

        $writersRaw = $cfg['writers'] ?? [];
        $this->assertIsArray($writersRaw, 'writers must be an array');

        $writersList = [];
        foreach ($writersRaw as $w) {
            $this->assertIsArray($w, 'each writer must be an array');
            /** @var array<string,mixed> $w */
            $writersList[] = $w;
        }

        /** @var array{channel:string, writers:list<array<string,mixed>>} $typed */
        $typed = ['channel' => $channel, 'writers' => $writersList];

        return $typed;
    }
}
