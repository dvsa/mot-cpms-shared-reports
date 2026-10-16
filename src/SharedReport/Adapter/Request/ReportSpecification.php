<?php

declare(strict_types=1);

namespace SharedReport\Adapter\Request;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * Class ReportSpecification
 *
 * @package SharedReport\Adapter\Request
 */
final class ReportSpecification
{
    private mixed $reference;
    private string $format;
    private DateTimeImmutable $toDate;
    private DateTimeImmutable $fromDate;

    public function __construct(mixed $typeReference, string $format = 'csv')
    {
        $this->reference = $typeReference;
        $this->format    = $format;
        $this->setDateFilters(new \DateTimeImmutable('-1 month'), new \DateTimeImmutable());
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getTypeReference(): mixed
    {
        return $this->reference;
    }

    /**
     * @return DateTimeInterface The to date filter in UTC timezone
     */
    public function getToDateFilter(): DateTimeInterface
    {
        return $this->toDate;
    }

    /**
     * @return DateTimeInterface The from date filter in UTC timezone
     */
    public function getFromDateFilter(): DateTimeInterface
    {
        return $this->fromDate;
    }

    /**
     * Sets date filters. Automatically converts them to UTC internally.
     *
     * @param DateTimeInterface $fromDate
     * @param DateTimeInterface $toDate
     *
     * @throws \Exception
     */
    public function setDateFilters(DateTimeInterface $fromDate, DateTimeInterface $toDate): void
    {
        if ($toDate->getTimestamp() > time()) {
            throw new \InvalidArgumentException('To date cannot be in the future');
        }
        if ($fromDate >= $toDate) {
            throw new \InvalidArgumentException('From date must be before To date');
        }

        $this->toDate   = new DateTimeImmutable($toDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
        $this->fromDate = new DateTimeImmutable($fromDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
    }
}
