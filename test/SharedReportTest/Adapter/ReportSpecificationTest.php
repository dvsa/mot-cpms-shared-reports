<?php

namespace SharedReportTest\Adapter;

use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\Request\ReportSpecification;

/**
 * Class ReportSpecificationTest
 *
 * @package SharedReportTest\Adapter
 */
class ReportSpecificationTest extends TestCase
{
    public function testCanGetTypeReference()
    {
        $spec = new ReportSpecification('testReference512');
        $this->assertSame('testReference512', $spec->getTypeReference());

        return $spec;
    }

    public function testCannotSetToDateInFuture()
    {
        $spec = new ReportSpecification('testReference512');
        $date = new \DateTime('+1 second');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("To date cannot be in the future");
        $spec->setDateFilters(new \DateTime('-1 month'), $date);
    }

    public function testCannotSetFromDateToCurrentTime()
    {
        $spec = new ReportSpecification('testReference512');
        $date = new \DateTime();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters(new \DateTime(), $date);
    }

    public function testCannotSetFromDateInFuture()
    {
        $spec = new ReportSpecification('testReference512');
        $date = new \DateTime('+1 second');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date, new \DateTime('now'));
    }

    public function testCannotSetFromDateThatIsAfterToDate()
    {
        $spec = new ReportSpecification('testReference512');
        $date = new \DateTimeImmutable('-1 day');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date->add(new \DateInterval('PT1S')), $date);
    }

    public function testCannotSetToDateThatIsBeforeFromDate()
    {
        $spec = new ReportSpecification('testReference512');
        $date = new \DateTimeImmutable('-1 day');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date, $date->sub(new \DateInterval('PT1S')));
    }
}
