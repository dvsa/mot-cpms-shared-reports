<?php

namespace SharedReport\Service;

use SharedReport\Entity\ReportStatus;

/**
 * Interface ReportServiceInterface
 *
 * @package SharedReport\Service
 */
interface ReportServiceInterface
{
    /**
     * @return @return array|AvailableReport[]
     */
    public function getAvailableReports();

    /**
     * @param $reportTypeReference
     * @param $format
     * @param $fromDate
     * @param $toDate
     *
     * @return ReportStatus
     */
    public function createReport($reportTypeReference, $format, $fromDate, $toDate);

    /**
     * @param $reportReference
     *
     * @return ReportStatus
     */
    public function getStatus($reportReference);

    /**
     * Get the output file
     *
     * @param string $reportIdentifier
     *
     * @return mixed
     */
    public function getOutputFile($reportIdentifier);
}
