<?php

namespace SharedReportTest\Service\FileHandler;

use PHPUnit\Framework\TestCase;
use SharedReport\Service\FileHandler\StoreLocally;

/**
 * Class StoreLocallyTest
 *
 * @package SharedReportTest\Service\FileHandler
 */
class StoreLocallyTest extends TestCase
{
    public function testThrowsExceptionIfNotPassedAStream()
    {
        $filename = uniqid('/tmp/test');
        $handler  = new StoreLocally($filename);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected a resource");
        $handler->handleFile('string');
    }

    public function testThrowsExceptionIfFileCannotBeCreated()
    {
        $filename = uniqid('/tmp/test') . '/what?/file';
        $handler  = new StoreLocally($filename);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Could not create file for writing");
        $handler->handleFile(fopen('php://memory', 'w+'));
    }

    public function testHandleFile()
    {
        $filename = uniqid('/tmp/test');
        $contents = uniqid('test-content');
        $stream   = fopen('php://temp', 'w+');
        fwrite($stream, $contents);
        $handler = new StoreLocally($filename);
        $handler->handleFile($stream);

        $this->assertEquals($contents, file_get_contents($filename));
    }

    public function testTruncatesFileIfItExistsAlreadyWhenWriting()
    {
        $filename = uniqid('/tmp/test');
        file_put_contents($filename, 'some data');
        $handler  = new StoreLocally($filename);
        $stream   = fopen('php://temp', 'w+');
        $contents = uniqid('test-content');
        fwrite($stream, $contents);
        $handler->handleFile($stream);
        $this->assertEquals($contents, file_get_contents($filename));
    }

    public function testGetCachedFileReturnsFilenameWhenFileExists()
    {
        $filename = uniqid('/tmp/test');
        touch($filename);
        $handler = new StoreLocally($filename);
        $this->assertEquals($filename, $handler->getCachedFile());
    }

    public function testGetCachedFileReturnsFalseWhenFileDoesNotExist()
    {
        $filename = uniqid('/tmp/test');
        $handler  = new StoreLocally($filename);
        $this->assertFalse($handler->getCachedFile());
    }
}
