<?php

namespace SharedReport\Entity;

use DateTimeInterface;

/**
 * Class ReportStatus
 *
 * @package SharedReport\Entity
 */
class ReportStatus
{
    private $totalRows;
    private $processedRows;
    private $reference;
    private $fromDateFilter;
    private $toDateFilter;
    private $readyForDownload;
    private $downloadFileSize;

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
        $reference,
        AvailableReport $reportDetails,
        DateTimeInterface $fromDateFilter,
        DateTimeInterface $toDateFilter,
        $totalRows,
        $processedRows,
        $readyForDownload,
        $downloadFileSize
    )
    {
        $this->reportDetails    = $reportDetails;
        $this->fromDateFilter   = $fromDateFilter;
        $this->toDateFilter     = $toDateFilter;
        $this->totalRows        = (int)$totalRows;
        $this->processedRows    = (int)$processedRows;
        $this->reference        = $reference;
        $this->readyForDownload = (bool)$readyForDownload;
        $this->downloadFileSize = $downloadFileSize;
    }

    /**
     * @return int
     */
    public function getDownloadFileSize()
    {
        return $this->downloadFileSize;
    }

    /**
     * @return int The percentage of rows processed
     */
    public function getPercentComplete()
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
    public function isComplete()
    {
        return $this->processedRows == $this->totalRows;
    }

    /**
     * @return bool Whether the report is ready for download
     */
    public function isReadyForDownload()
    {
        return $this->readyForDownload;
    }

    /**
     * @return string The identifier for the report
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getTotalRowCount()
    {
        return $this->totalRows;
    }

    /**
     * @return int
     */
    public function getProcessedRowCount()
    {
        return $this->processedRows;
    }

    /**
     * @return string The type of report name
     */
    public function getReportTypeName()
    {
        return $this->reportDetails->getName();
    }

    /**
     * @return string The type of report code
     */
    public function getReportTypeCode()
    {
        return $this->reportDetails->getReference();
    }

    /**
     * @return \DateTimeImmutable The From date filter
     */
    public function getFromDateFilter()
    {
        return $this->fromDateFilter;
    }

    /**
     * @return \DateTimeImmutable The To date filter
     */
    public function getToDateFilter()
    {
        return $this->toDateFilter;
    }
}
