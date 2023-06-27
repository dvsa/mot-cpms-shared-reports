<?php

namespace SharedReport\Service\FileHandler;

/**
 * Class StoreLocally
 *
 * @package SharedReport\Service\FileHandler
 */
class StoreLocally implements FileHandlerInterface
{
    /** @var  string */
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param resource $stream
     *
     * @return string
     */
    public function handleFile($stream)
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

    public function getCachedFile()
    {
        if (file_exists($this->filename)) {
            return $this->filename;
        }

        return false;
    }
}
