<?php

namespace SharedReport\Adapter\Response;

use DateTimeImmutable;

/**
 * Class ReportStatus
 *
 * @package SharedReport\Adapter\Response
 */
class ReportStatus extends AbstractResponse
{
    protected $complete;
    protected $reference;
    protected $totalRowCount;
    protected $processedRowCount;
    protected $downloadFileSize;
    protected $reportTypeName;
    protected $reportTypeCode;
    protected $fromDateFilter;
    protected $toDateFilter;

    public function __construct(
        $success,
        $reference,
        $reportTypeName,
        $reportTypeCode,
        DateTimeImmutable $fromDateFilter,
        DateTimeImmutable $toDateFilter,
        $complete,
        $totalRowCount,
        $processedRowCount,
        $downloadFileSize
    )
    {
        $this->success           = $success;
        $this->reference         = $reference;
        $this->reportTypeName    = $reportTypeName;
        $this->reportTypeCode    = $reportTypeCode;
        $this->fromDateFilter    = $fromDateFilter;
        $this->toDateFilter      = $toDateFilter;
        $this->complete          = (bool)$complete;
        $this->totalRowCount     = $totalRowCount;
        $this->processedRowCount = $processedRowCount;
        $this->downloadFileSize  = $downloadFileSize;
    }

    /**
     * @return boolean
     */
    public function isComplete()
    {
        return $this->complete;
    }

    /**
     * @return string
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
        return $this->totalRowCount;
    }

    /**
     * @return int
     */
    public function getProcessedRowCount()
    {
        return $this->processedRowCount;
    }

    /**
     * @return int The file download size in Bytes
     */
    public function getDownloadFileSize()
    {
        return $this->downloadFileSize;
    }

    /**
     * @return string
     */
    public function getReportTypeName()
    {
        return $this->reportTypeName;
    }

    public function getReportTypeCode()
    {
        return $this->reportTypeCode;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFromDateFilter()
    {
        return $this->fromDateFilter;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getToDateFilter()
    {
        return $this->toDateFilter;
    }
}
