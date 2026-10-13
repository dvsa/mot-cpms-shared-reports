<?php

declare(strict_types=1);

namespace SharedReport\Service;

use SharedReport\Entity\AvailableReport;
use SharedReport\Entity\ReportStatus;

/**
 * Interface ReportServiceInterface
 *
 * @package SharedReport\Service
 */
interface ReportServiceInterface
{
    /**
     * @return array<AvailableReport>
     */
    public function getAvailableReports(): array;

    /**
     * @param string $reportTypeReference
     * @param string $format
     * @param string $fromDate
     * @param string $toDate
     *
     * @return ReportStatus
     */
    public function createReport(string $reportTypeReference, string $format, string $fromDate, string $toDate): ReportStatus;

    /**
     * @param string|int $reportReference
     *
     * @return ReportStatus
     */
    public function getStatus(string|int $reportReference): ReportStatus;

    /**
     * Get the output file
     *
     * @param string $reportIdentifier
     *
     * @return mixed
     */
    public function getOutputFile(string $reportIdentifier): mixed;
}
