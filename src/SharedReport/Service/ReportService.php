<?php

namespace SharedReport\Service;

use SharedReport\Adapter\ReportServiceAdapterInterface;
use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\ReportStatus as ReportStatusResponse;
use SharedReport\Entity\AvailableReport;
use SharedReport\Entity\ReportStatus;
use SharedReport\Service\FileHandler\FileHandlerInterface;

/**
 * Class ReportService
 *
 * @package SharedReport\Service
 */
class ReportService implements ReportServiceInterface
{
    protected $client;
    /** @var  FileHandlerInterface */
    protected $fileHandler;

    public function __construct(ReportServiceAdapterInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Request a report
     *
     * @param string $reportTypeReference
     * @param string $format   Format of the report. Currently only csv works.
     * @param string $fromDate The from date filter as a string. Time will be set to 00:00:00
     * @param string $toDate   The to date filter as a string. Time will be set to 23:59:59
     *
     * @return ReportStatus The status of the newly created report
     * @throws \Exception
     */
    public function createReport($reportTypeReference, $format, $fromDate, $toDate)
    {
        // As the dates will be having their times altered, the timezone can be set to UTC
        $fromDate = new \DateTimeImmutable($fromDate, new \DateTimeZone('UTC'));
        $fromDate = $fromDate->setTime(0, 0, 0);
        $toDate   = new \DateTimeImmutable($toDate, new \DateTimeZone('UTC'));
        $toDate   = $toDate->setTime(23, 59, 59);
        if ($toDate->getTimestamp() > time()) {
            $toDate = new \DateTimeImmutable();
        }
        $reportSpec = new ReportSpecification($reportTypeReference, $format);
        $reportSpec->setDateFilters($fromDate, $toDate);

        $response = $this->client->createReportRequest($reportSpec);

        return $this->getStatusFromResponse($response);
    }

    /**
     * @return array|AvailableReport[]
     */
    public function getAvailableReports()
    {
        $response = $this->client->fetchAvailableReports();

        return $response->getAvailableReports();
    }

    /**
     * Get the status of a specific report
     *
     * @param $reportIdentifier
     *
     * @return ReportStatus
     */
    public function getStatus($reportIdentifier)
    {
        $response = $this->client->fetchReportStatus($reportIdentifier);

        return $this->getStatusFromResponse($response);
    }

    protected function getStatusFromResponse(ReportStatusResponse $response)
    {
        $reportDetails = new AvailableReport(
            $response->getReportTypeName(),
            $response->getReportTypeCode()
        );

        return new ReportStatus(
            $response->getReference(),
            $reportDetails,
            $response->getFromDateFilter(),
            $response->getToDateFilter(),
            $response->getTotalRowCount(),
            $response->getProcessedRowCount(),
            $response->isComplete(),
            $response->getDownloadFileSize()
        );
    }

    /**
     * Get the output file, after processing by whatever file handler has been configured for the service.
     *
     * @param string $reportIdentifier
     *
     * @return mixed Response from File Handler
     */
    public function getOutputFile($reportIdentifier)
    {
        if (!$this->fileHandler instanceof FileHandlerInterface) {
            throw new \RuntimeException('File Handler strategy not defined.');
        }
        $file = $this->fileHandler->getCachedFile();
        if ($file === false) {
            $handle = $this->client->fetchReportContents($reportIdentifier);
            $file   = $this->fileHandler->handleFile($handle);
        }

        return $file;
    }

    /**
     * Set the file handler to use for processing of the report file from the remote service
     *
     * @param FileHandlerInterface $fileHandler
     */
    public function setFileHandler(FileHandlerInterface $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }
}
