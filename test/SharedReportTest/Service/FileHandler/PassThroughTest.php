<?php

declare(strict_types=1);

namespace SharedReportTest\Service\FileHandler;

use PHPUnit\Framework\TestCase;
use SharedReport\Service\FileHandler\PassThrough;

/**
 * Class PassThroughTest
 *
 * @package SharedReportTest\Service\FileHandler
 */
final class PassThroughTest extends TestCase
{
    public function testThrowsExceptionIfNotPassedAStream(): void
    {
        $handler = new PassThrough();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected a resource");
        /** @psalm-suppress InvalidArgument */
        $handler->handleFile('string');
    }

    public function testGetCachedFileReturnsFalse(): void
    {
        $handler = new PassThrough();
        $this->assertFalse($handler->getCachedFile());
    }
}
