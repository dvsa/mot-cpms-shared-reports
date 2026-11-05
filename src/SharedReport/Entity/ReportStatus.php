<?php

declare(strict_types=1);

namespace SharedReport\Entity;

use DateTimeInterface;

/**
 * Class ReportStatus
 *
 * @api
 *
 * @package SharedReport\Entity
 */
final class ReportStatus
{
    public function __construct(
        private string $reference,
        private AvailableReport $reportDetails,
        private DateTimeInterface $fromDateFilter,
        private DateTimeInterface $toDateFilter,
        private int $totalRows,
        private int $processedRows,
        private bool $readyForDownload,
        private int $downloadFileSize
    ) {
    }

    /**
     * @return int
     */
    public function getDownloadFileSize(): int
    {
        return $this->downloadFileSize;
    }

    /**
     * @return int The percentage of rows processed
     */
    public function getPercentComplete(): int
    {
        if ($this->processedRows == $this->totalRows) {
            return 100;
        }
        if ($this->processedRows == 0) {
            return 0;
        }

        return (int)round(100 * $this->processedRows / $this->totalRows);
    }

    /**
     * @return bool Whether the report is complete
     */
    public function isComplete(): bool
    {
        return $this->processedRows == $this->totalRows;
    }

    /**
     * @return bool Whether the report is ready for download
     */
    public function isReadyForDownload(): bool
    {
        return $this->readyForDownload;
    }

    /**
     * @return string The identifier for the report
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
        return $this->totalRows;
    }

    /**
     * @return int
     */
    public function getProcessedRowCount(): int
    {
        return $this->processedRows;
    }

    /**
     * @return string The type of report name
     */
    public function getReportTypeName(): string
    {
        return $this->reportDetails->getName();
    }

    /**
     * @return string The type of report code
     */
    public function getReportTypeCode(): string
    {
        return $this->reportDetails->getReference();
    }

    /**
     * @return DateTimeInterface The From date filter
     */
    public function getFromDateFilter(): DateTimeInterface
    {
        return $this->fromDateFilter;
    }

    /**
     * @return DateTimeInterface The To date filter
     */
    public function getToDateFilter(): DateTimeInterface
    {
        return $this->toDateFilter;
    }
}
