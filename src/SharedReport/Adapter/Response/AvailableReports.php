<?php

namespace SharedReport\Adapter\Response;

use SharedReport\Entity\AvailableReport;

/**
 * Class AvailableReports
 *
 * @package SharedReport\Adapter\Response
 */
class AvailableReports extends AbstractResponse
{
    protected $success;
    private $availableReports = [];

    public function __construct($success)
    {
        $this->success = $success;
    }

    public function addReport($name, $reference, $description = '')
    {
        $this->availableReports[] = new AvailableReport($name, $reference, $description);
    }

    public function getAvailableReports()
    {
        return $this->availableReports;
    }
}
