<?php

declare(strict_types=1);

namespace SharedReportTest\Service\FileHandler;

use PHPUnit\Framework\TestCase;
use SharedReport\Service\FileHandler\StoreLocally;

/**
 * Class StoreLocallyTest
 *
 * @package SharedReportTest\Service\FileHandler
 */
final class StoreLocallyTest extends TestCase
{
    public function testThrowsExceptionIfNotPassedAStream(): void
    {
        $filename = uniqid('/tmp/test');
        $handler  = new StoreLocally($filename);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected a resource");
        /** @psalm-suppress InvalidArgument */
        $handler->handleFile('string');
    }

    public function testThrowsExceptionIfFileCannotBeCreated(): void
    {
        $filename = uniqid('/tmp/test') . '/what?/file';
        $handler  = new StoreLocally($filename);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Could not create file for writing");
        $handler->handleFile(fopen('php://memory', 'w+'));
    }

    public function testHandleFile(): void
    {
        $filename = uniqid('/tmp/test');
        $contents = uniqid('test-content');
        /* @var resource|false $stream */
        $stream   = fopen('php://temp', 'w+');
        $this->assertIsResource($stream);
        /** @var resource $stream */
        fwrite($stream, $contents);
        $handler = new StoreLocally($filename);
        $handler->handleFile($stream);

        $this->assertEquals($contents, file_get_contents($filename));
    }

    public function testTruncatesFileIfItExistsAlreadyWhenWriting(): void
    {
        $filename = uniqid('/tmp/test');
        file_put_contents($filename, 'some data');
        $handler  = new StoreLocally($filename);
        /* @var resource|false $stream */
        $stream   = fopen('php://temp', 'w+');
        $this->assertIsResource($stream);
        /** @var resource $stream */
        $contents = uniqid('test-content');
        fwrite($stream, $contents);
        $handler->handleFile($stream);
        $this->assertEquals($contents, file_get_contents($filename));
    }

    public function testGetCachedFileReturnsFilenameWhenFileExists(): void
    {
        $filename = uniqid('/tmp/test');
        touch($filename);
        $handler = new StoreLocally($filename);
        $this->assertEquals($filename, $handler->getCachedFile());
    }

    public function testGetCachedFileReturnsFalseWhenFileDoesNotExist(): void
    {
        $filename = uniqid('/tmp/test');
        $handler  = new StoreLocally($filename);
        $this->assertFalse($handler->getCachedFile());
    }
}
