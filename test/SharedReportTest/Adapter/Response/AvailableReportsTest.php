<?php

namespace SharedReportTest\Adapter\Response;

use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\Response\AvailableReports;

/**
 * Class AvailableReportsTest
 *
 * @package SharedReportTest\Adapter\Response
 */
class AvailableReportsTest extends TestCase
{
    public function testIsNotFailedWhenSuccessTrue()
    {
        $response = new AvailableReports(true);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isFailure());
    }

    public function testIsFailedWhenSuccessFalse()
    {
        $response = new AvailableReports(false);
        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isFailure());
    }
}
