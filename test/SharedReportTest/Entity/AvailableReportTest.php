<?php

namespace SharedReportTest\Entity;

use PHPUnit\Framework\TestCase;
use SharedReport\Entity\AvailableReport;

/**
 * Class AvailableReportTest
 *
 * @package SharedReportTest\Entity
 */
class AvailableReportTest extends TestCase
{
    public function testGetName()
    {
        $testName = uniqid('test-name');
        $object   = new AvailableReport($testName, '123');
        $this->assertEquals($testName, $object->getName());
    }

    public function testGetReference()
    {
        $testReference = uniqid('test-reference');
        $object        = new AvailableReport('a name', $testReference);
        $this->assertEquals($testReference, $object->getReference());
    }

    public function testDescription()
    {
        $testDescription = uniqid('test-description');
        $object          = new AvailableReport('name', '123', $testDescription);
        $this->assertEquals($testDescription, $object->getDescription());
    }
}
