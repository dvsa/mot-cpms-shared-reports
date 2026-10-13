<?php

declare(strict_types=1);

namespace SharedReportTest\Entity;

use PHPUnit\Framework\TestCase;
use SharedReport\Entity\AvailableReport;

/**
 * Class AvailableReportTest
 *
 * @package SharedReportTest\Entity
 */
final class AvailableReportTest extends TestCase
{
    public function testGetName(): void
    {
        $testName = uniqid('test-name');
        $object   = new AvailableReport($testName, '123');
        $this->assertEquals($testName, $object->getName());
    }

    public function testGetReference(): void
    {
        $testReference = uniqid('test-reference');
        $object        = new AvailableReport('a name', $testReference);
        $this->assertEquals($testReference, $object->getReference());
    }

    public function testDescription(): void
    {
        $testDescription = uniqid('test-description');
        $object          = new AvailableReport('name', '123', $testDescription);
        $this->assertEquals($testDescription, $object->getDescription());
    }
}
