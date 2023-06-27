<?php

namespace SharedReport\Adapter\Response;

/**
 * Class AbstractResponse
 *
 * @package SharedReport\Adapter\Response
 */
abstract class AbstractResponse
{
    protected $success;

    public function isSuccess()
    {
        return $this->success;
    }

    public function isFailure()
    {
        return $this->success != true;
    }
}
