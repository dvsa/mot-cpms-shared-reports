<?php

declare(strict_types=1);

namespace SharedReportTest\Adapter;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\Request\ReportSpecification;

/**
 * Class ReportSpecificationTest
 *
 * @package SharedReportTest\Adapter
 */
final class ReportSpecificationTest extends TestCase
{
    public function testCanGetTypeReference(): ReportSpecification
    {
        $spec = new ReportSpecification('testReference512');
        $this->assertSame('testReference512', $spec->getTypeReference());

        return $spec;
    }

    public function testCannotSetToDateInFuture(): void
    {
        $spec = new ReportSpecification('testReference512');
        $date = new DateTimeImmutable('+1 second');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("To date cannot be in the future");
        $spec->setDateFilters(new DateTimeImmutable('-1 month'), $date);
    }

    public function testCannotSetFromDateToCurrentTime(): void
    {
        $spec = new ReportSpecification('testReference512');
        $date = new DateTimeImmutable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters(new DateTimeImmutable(), $date);
    }

    public function testCannotSetFromDateInFuture(): void
    {
        $spec = new ReportSpecification('testReference512');
        $date = new DateTimeImmutable('+1 second');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date, new DateTimeImmutable('now'));
    }

    public function testCannotSetFromDateThatIsAfterToDate(): void
    {
        $spec = new ReportSpecification('testReference512');
        $date = new DateTimeImmutable('-1 day');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date->add(new \DateInterval('PT1S')), $date);
    }

    public function testCannotSetToDateThatIsBeforeFromDate(): void
    {
        $spec = new ReportSpecification('testReference512');
        $date = new DateTimeImmutable('-1 day');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("From date must be before To date");
        $spec->setDateFilters($date, $date->sub(new \DateInterval('PT1S')));
    }
}
