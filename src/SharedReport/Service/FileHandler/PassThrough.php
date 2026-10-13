<?php

declare(strict_types=1);

namespace SharedReport\Service\FileHandler;

/**
 * Class PassThrough
 * Represents a simple handler that just passes the file handle through for further processing or direct
 * streaming to the end user.
 *
 * @package SharedReport\Service\FileHandler
 */
final class PassThrough implements FileHandlerInterface
{
    /**
     * @param mixed $stream
     *
     * @return mixed
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    #[\Override]
    public function handleFile($stream): mixed
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
    #[\Override]
    public function getCachedFile(): bool
    {
        return false;
    }
}
