<?php

declare(strict_types=1);

namespace SharedReport\Entity;

use DateTimeInterface;

/**
 * Class ReportStatus
 *
 * @package SharedReport\Entity
 */
final class ReportStatus
{
    private int $totalRows;
    private int $processedRows;
    private string $reference;
    private DateTimeInterface $fromDateFilter;
    private DateTimeInterface $toDateFilter;
    private bool $readyForDownload;
    private int $downloadFileSize;
    private AvailableReport $reportDetails;

    /**
     * @param string            $reference
     * @param AvailableReport   $reportDetails
     * @param DateTimeInterface $fromDateFilter
     * @param DateTimeInterface $toDateFilter
     * @param int               $totalRows
     * @param int               $processedRows
     * @param bool              $readyForDownload
     * @param int               $downloadFileSize
     */
    public function __construct(
        string $reference,
        AvailableReport $reportDetails,
        DateTimeInterface $fromDateFilter,
        DateTimeInterface $toDateFilter,
        int $totalRows,
        int $processedRows,
        bool $readyForDownload,
        int $downloadFileSize
    ) {
        $this->reportDetails    = $reportDetails;
        $this->fromDateFilter   = $fromDateFilter;
        $this->toDateFilter     = $toDateFilter;
        $this->totalRows        = $totalRows;
        $this->processedRows    = $processedRows;
        $this->reference        = $reference;
        $this->readyForDownload = $readyForDownload;
        $this->downloadFileSize = $downloadFileSize;
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
