<?php

namespace SharedReport\Adapter\Request;

use DateTime;
use DateTimeInterface;

/**
 * Class ReportSpecification
 *
 * @package SharedReport\Adapter\Request
 */
class ReportSpecification
{
    private $reference;
    private $format;
    private $toDate;
    private $fromDate;

    public function __construct($typeReference, $format = 'csv')
    {
        $this->reference = $typeReference;
        $this->format    = $format;
        $this->setDateFilters(new \DateTimeImmutable('-1 month'), new \DateTimeImmutable());
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getTypeReference()
    {
        return $this->reference;
    }

    /**
     * @return DateTimeInterface The to date filter in UTC timezone
     */
    public function getToDateFilter()
    {
        return $this->toDate;
    }

    /**
     * @return DateTimeInterface The from date filter in UTC timezone
     */
    public function getFromDateFilter()
    {
        return $this->fromDate;
    }

    /**
     * Sets date filters. Automatically converts them to UTC internally.
     *
     * @param DateTimeInterface|DateTime $fromDate
     * @param DateTimeInterface|DateTime $toDate
     *
     * @throws
     */
    public function setDateFilters(DateTimeInterface $fromDate, DateTimeInterface $toDate)
    {
        if ($toDate->getTimestamp() > time()) {
            throw new \InvalidArgumentException('To date cannot be in the future');
        }
        if ($fromDate >= $toDate) {
            throw new \InvalidArgumentException('From date must be before To date');
        }

        $this->toDate   = $toDate->setTimezone(new \DateTimeZone('UTC'));
        $this->fromDate = $fromDate->setTimezone(new \DateTimeZone('UTC'));
    }
}
