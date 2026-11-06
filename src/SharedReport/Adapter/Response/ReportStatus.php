<?php

declare(strict_types=1);

namespace SharedReport\Adapter\Response;

use DateTimeImmutable;

/**
 * Class ReportStatus
 *
 * @api
 *
 * @package SharedReport\Adapter\Response
 */
final class ReportStatus extends AbstractResponse
{
    public function __construct(
        protected bool $success,
        protected string $reference,
        protected string $reportTypeName,
        protected string $reportTypeCode,
        protected DateTimeImmutable $fromDateFilter,
        protected DateTimeImmutable $toDateFilter,
        protected bool $complete,
        protected int $totalRowCount,
        protected int $processedRowCount,
        protected int $downloadFileSize
    ) {
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getTotalRowCount(): int
    {
        return $this->totalRowCount;
    }

    /**
     * @return int
     */
    public function getProcessedRowCount(): int
    {
        return $this->processedRowCount;
    }

    /**
     * @return int The file download size in Bytes
     */
    public function getDownloadFileSize(): int
    {
        return $this->downloadFileSize;
    }

    /**
     * @return string
     */
    public function getReportTypeName(): string
    {
        return $this->reportTypeName;
    }

    public function getReportTypeCode(): string
    {
        return $this->reportTypeCode;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFromDateFilter(): DateTimeImmutable
    {
        return $this->fromDateFilter;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getToDateFilter(): DateTimeImmutable
    {
        return $this->toDateFilter;
    }
}
