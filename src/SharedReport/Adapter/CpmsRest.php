<?php

namespace SharedReport\Adapter;

use CpmsClient\Service\ApiService;
use DateTimeImmutable;
use DateTimeZone;
use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\AvailableReports;
use SharedReport\Adapter\Response\ReportStatus;

/**
 * Class CpmsRest
 *
 * @package SharedReport\Adapter
 */
class CpmsRest implements ReportServiceAdapterInterface
{
    const START_DATE_FILTER_KEY = 'from';
    const TO_DATE_FILTER_KEY    = 'to';

    const REFERENCE_PREFIX = 'CPMS';

    protected $apiService;

    public function __construct(ApiService $restApiService)
    {
        $this->apiService = $restApiService;
    }

    /**
     * Fetches all available reports
     *
     * @return AvailableReports An array of AvailableReport objects.
     * @throws \Exception
     */
    public function fetchAvailableReports()
    {
        $endpoint = 'api/report';
        $params   = [
            'required_fields' => [
                'name',
                'title',
                'code',
                'description'
            ]
        ];
        $response = $this->apiService->get($endpoint, ApiService::SCOPE_REPORT, $params);
        $this->validateResponse($response);

        $reports = new AvailableReports(true);
        foreach ($response['items'] as $row) {
            $name = $code = $description = '';
            if (isset($row['name'])) {
                $name = $row['name'];
            } elseif (isset($row['title'])) {
                $name = $row['title'];
            }
            if (isset($row['code'])) {
                $code = $row['code'];
            }
            if (isset($row['description'])) {
                $description = $row['description'];
            }

            $reports->addReport($name, $this->formatReportCode($code), $description);
        }

        return $reports;
    }

    /**
     * Requests a report from the API
     *
     * @param $reportSpecRequest
     *
     * @return ReportStatus
     * @throws \Exception
     */
    public function createReportRequest(ReportSpecification $reportSpecRequest)
    {
        $endpoint = 'api/report';
        $data     = [
            'report_code' => $this->formatReportCode($reportSpecRequest->getTypeReference(), true),
            'format'      => $reportSpecRequest->getFormat(),
            'filters'     => [
                'from' => $reportSpecRequest->getFromDateFilter()->format('Y-m-d H:i:s'),
                'to'   => $reportSpecRequest->getToDateFilter()->format('Y-m-d H:i:s')
            ]
        ];
        $response = $this->apiService->post($endpoint, ApiService::SCOPE_REPORT, $data);
        $this->validateResponse($response);

        return $this->fetchReportStatus($response['reference']);
    }

    /**
     * Fetches the latest status for a requested report
     *
     * @param $reportIdentifier
     *
     * @return ReportStatus
     * @throws \Exception
     */
    public function fetchReportStatus($reportIdentifier)
    {
        $endpoint = "api/report/$reportIdentifier/status";

        $response = $this->apiService->get($endpoint, ApiService::SCOPE_REPORT);
        $this->validateResponse($response);

        return new ReportStatus(
            empty($response['error']),
            $reportIdentifier,
            $response['report_type'],
            $this->formatReportCode($response['report_type_code']),
            new DateTimeImmutable($response['report_filters']['from'], new DateTimeZone('UTC')),
            new DateTimeImmutable($response['report_filters']['to'], new DateTimeZone('UTC')),
            $response['completed'],
            $response['total_rows'],
            $response['processed_rows'],
            $response['download_size']
        );
    }

    /**
     * Fetches report contents.
     *
     * @param int|string $reportIdentifier
     *
     * @return mixed
     */
    public function fetchReportContents($reportIdentifier)
    {
        $endpoint = "api/report/$reportIdentifier/download";
        $response = $this->apiService->get($endpoint, ApiService::SCOPE_REPORT);
        if (is_array($response)) {
            throw new ApiAdapterException($response['message'], $response['code']);
        }

        $file = fopen('php://temp', 'w+');
        fwrite($file, $response);
        rewind($file);

        return $file;
    }

    protected function validateResponse($response)
    {
        if (is_array($response) === false) {
            throw new ApiAdapterException('Received invalid response from API: ' . htmlentities($response));
        }
        if (array_key_exists('code', $response) && $response['code'] != '000') {
            throw new ApiAdapterException($response['message'], $response['code']);
        }
    }

    private function formatReportCode($code, $stripPrefix = false)
    {
        if ($stripPrefix) {
            return str_replace(self::REFERENCE_PREFIX, '', $code);
        }

        return self::REFERENCE_PREFIX . $code;
    }
}
