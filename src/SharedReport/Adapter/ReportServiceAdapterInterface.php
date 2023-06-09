<?php

namespace SharedReport\Adapter;

use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\AvailableReports;
use SharedReport\Adapter\Response\ReportStatus;

/**
 * Interface ReportServiceAdapterInterface
 *
 * @package SharedReport\Adapter
 */
interface ReportServiceAdapterInterface
{
    /**
     * Fetches all available reports
     *
     * @return AvailableReports
     */
    public function fetchAvailableReports();

    /**
     * Requests a report from the API
     *
     * @param  ReportSpecification $reportSpec
     *
     * @return ReportStatus
     */
    public function createReportRequest(ReportSpecification $reportSpec);

    /**
     * Fetches the latest status for a requested report
     *
     * @param  string|int $reportIdentifier
     *
     * @return ReportStatus
     */
    public function fetchReportStatus($reportIdentifier);

    /**
     * Fetches report contents.
     *
     * @param  string|int $reportIdentifier
     *
     * @return resource
     */
    public function fetchReportContents($reportIdentifier);
}
