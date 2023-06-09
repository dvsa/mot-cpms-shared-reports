<?php

namespace SharedReportTest\Service\FileHandler;

use PHPUnit\Framework\TestCase;
use SharedReport\Service\FileHandler\PassThrough;

/**
 * Class PassThroughTest
 *
 * @package SharedReportTest\Service\FileHandler
 */
class PassThroughTest extends TestCase
{
    public function testThrowsExceptionIfNotPassedAStream()
    {
        $handler = new PassThrough();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected a resource");
        $handler->handleFile('string');
    }

    public function testGetCachedFileReturnsFalse()
    {
        $handler = new PassThrough();
        $this->assertFalse($handler->getCachedFile());
    }
}
