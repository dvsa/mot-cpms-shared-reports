<?php

declare(strict_types=1);

namespace SharedReport\Service\FileHandler;

/**
 * Class StoreLocally
 *
 * @package SharedReport\Service\FileHandler
 */
final class StoreLocally implements FileHandlerInterface
{
    protected string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param mixed $stream
     *
     * @return string
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    #[\Override]
    public function handleFile($stream): string
    {
        if (is_resource($stream) === false) {
            throw new \InvalidArgumentException('Expected a resource');
        }
        rewind($stream);

        $handle = @fopen($this->filename, 'w+');
        if ($handle === false) {
            throw new \RuntimeException("Could not create file for writing");
        }

        stream_copy_to_stream($stream, $handle);
        fclose($handle);

        return $this->filename;
    }

    #[\Override]
    public function getCachedFile(): bool|string
    {
        if (file_exists($this->filename)) {
            return $this->filename;
        }

        return false;
    }
}
