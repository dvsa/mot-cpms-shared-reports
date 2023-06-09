<?php

namespace SharedReport\Service\FileHandler;

/**
 * Class PassThrough
 * Represents a simple handler that just passes the file handle through for further processing or direct
 * streaming to the end user.
 *
 * @package SharedReport\Service\FileHandler
 */
class PassThrough implements FileHandlerInterface
{
    /**
     * @param resource $stream
     *
     * @return mixed
     */
    public function handleFile($stream)
    {
        if (is_resource($stream) === false) {
            throw new \InvalidArgumentException('Expected a resource');
        }

        return $stream;
    }

    /**
     * This class cannot cache the file, therefore always returns false.
     *
     * @return bool
     */
    public function getCachedFile()
    {
        return false;
    }
}
