<?php

declare(strict_types=1);

namespace SharedReport\Adapter\Response;

/**
 * Class AbstractResponse
 *
 * @package SharedReport\Adapter\Response
 */
abstract class AbstractResponse
{
    protected bool $success;

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailure(): bool
    {
        return $this->success !== true;
    }
}
