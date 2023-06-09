<?php

namespace SharedReport\Service\FileHandler;

/**
 * Interface FileHandlerInterface
 *
 * @package SharedReport\Service\FileHandler
 */
interface FileHandlerInterface
{
    /**
     * @param resource $stream
     *
     * @return mixed
     */
    public function handleFile($stream);

    public function getCachedFile();
}
