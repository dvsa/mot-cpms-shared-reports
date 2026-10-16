<?php

declare(strict_types=1);

namespace SharedReport\Adapter\Response;

use DateTimeImmutable;

/**
 * Class ReportStatus
 *
 * @package SharedReport\Adapter\Response
 */
final class ReportStatus extends AbstractResponse
{
    protected bool $complete;
    protected string $reference;
    protected int $totalRowCount;
    protected int $processedRowCount;
    protected int $downloadFileSize;
    protected string $reportTypeName;
    protected string $reportTypeCode;
    protected DateTimeImmutable $fromDateFilter;
    protected DateTimeImmutable $toDateFilter;

    public function __construct(
        bool $success,
        string $reference,
        string $reportTypeName,
        string $reportTypeCode,
        DateTimeImmutable $fromDateFilter,
        DateTimeImmutable $toDateFilter,
        bool $complete,
        int $totalRowCount,
        int $processedRowCount,
        int $downloadFileSize
    ) {
        $this->success           = $success;
        $this->reference         = $reference;
        $this->reportTypeName    = $reportTypeName;
        $this->reportTypeCode    = $reportTypeCode;
        $this->fromDateFilter    = $fromDateFilter;
        $this->toDateFilter      = $toDateFilter;
        $this->complete          = $complete;
        $this->totalRowCount     = $totalRowCount;
        $this->processedRowCount = $processedRowCount;
        $this->downloadFileSize  = $downloadFileSize;
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
