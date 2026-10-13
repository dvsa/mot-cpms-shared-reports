<?php

declare(strict_types=1);

namespace SharedReport\Adapter;

use CpmsClient\Service\ApiService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Laminas\Cache\Exception\ExceptionInterface;
use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\AvailableReports;
use SharedReport\Adapter\Response\ReportStatus;

/**
 * Class CpmsRest
 *
 * @package SharedReport\Adapter
 */
final class CpmsRest implements ReportServiceAdapterInterface
{
    public const START_DATE_FILTER_KEY = 'from';
    public const TO_DATE_FILTER_KEY    = 'to';

    public const REFERENCE_PREFIX = 'CPMS';

    public function __construct(protected ApiService $apiService)
    {
    }

    /**
     * Fetches all available reports
     *
     * @return AvailableReports An array of AvailableReport objects.
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[\Override]
    public function fetchAvailableReports(): AvailableReports
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

        /** @var array{items:list<array<string,mixed>>} $response */
        $reports = new AvailableReports(true);
        foreach ($response['items'] as $row) {
            $nameStr = '';
            if (isset($row['name']) && is_string($row['name'])) {
                $nameStr = $row['name'];
            } elseif (isset($row['title']) && is_string($row['title'])) {
                $nameStr = $row['title'];
            }

            $descriptionStr = '';
            if (isset($row['description']) && is_string($row['description'])) {
                $descriptionStr = $row['description'];
            }

            $codeRaw = $row['code'] ?? '';
            if (is_array($codeRaw)) {
                $json = json_encode($codeRaw);
                $codeValue = isset($codeRaw['code']) && is_string($codeRaw['code']) ? $codeRaw['code'] : ($json !== false ? $json : '');
            } elseif (is_scalar($codeRaw)) {
                $codeValue = (string) $codeRaw;
            } else {
                $codeValue = '';
            }

            $reports->addReport($nameStr, $this->formatReportCode($codeValue), $descriptionStr);
        }

        return $reports;
    }

    /**
     * Requests a report from the API
     *
     * @param ReportSpecification $reportSpec
     *
     * @return ReportStatus
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[\Override]
    public function createReportRequest(ReportSpecification $reportSpec): ReportStatus
    {
        $endpoint = 'api/report';
        $typeRef = $reportSpec->getTypeReference();
        $typeRefStr = is_scalar($typeRef) ? (string) $typeRef : '';

        $data     = [
            'report_code' => $this->formatReportCode($typeRefStr, true),
            'format'      => $reportSpec->getFormat(),
            'filters'     => [
                'from' => $reportSpec->getFromDateFilter()->format('Y-m-d H:i:s'),
                'to'   => $reportSpec->getToDateFilter()->format('Y-m-d H:i:s')
            ]
        ];
        $response = $this->apiService->post($endpoint, ApiService::SCOPE_REPORT, $data);
        $this->validateResponse($response);

        /** @var array{reference:mixed} $response */
        $reference = isset($response['reference']) && is_scalar($response['reference']) ? (string)$response['reference'] : '';
        return $this->fetchReportStatus($reference);
    }

    /**
     * Fetches the latest status for a requested report
     *
     * @param string|int $reportIdentifier
     *
     * @return ReportStatus
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[\Override]
    public function fetchReportStatus(string|int $reportIdentifier): ReportStatus
    {
        $endpoint = "api/report/$reportIdentifier/status";

        $response = $this->apiService->get($endpoint, ApiService::SCOPE_REPORT);
        $this->validateResponse($response);

        $reportType = isset($response['report_type']) && is_string($response['report_type']) ? $response['report_type'] : '';
        $reportTypeCode = isset($response['report_type_code']) && is_scalar($response['report_type_code']) ? (string) $response['report_type_code'] : '';

        $filters = is_array($response['report_filters'] ?? null) ? $response['report_filters'] : [];
        $from = isset($filters['from']) && is_string($filters['from']) ? $filters['from'] : '';
        $to = isset($filters['to']) && is_string($filters['to']) ? $filters['to'] : '';

        $completed = isset($response['completed']) ? (bool) $response['completed'] : false;
        $totalRows = isset($response['total_rows']) && is_numeric($response['total_rows']) ? (int) $response['total_rows'] : 0;
        $processedRows = isset($response['processed_rows']) && is_numeric($response['processed_rows']) ? (int) $response['processed_rows'] : 0;
        $downloadSize = isset($response['download_size']) && is_numeric($response['download_size']) ? (int) $response['download_size'] : 0;

        return new ReportStatus(
            empty($response['error']),
            (string)$reportIdentifier,
            $reportType,
            $this->formatReportCode($reportTypeCode),
            new DateTimeImmutable($from, new DateTimeZone('UTC')),
            new DateTimeImmutable($to, new DateTimeZone('UTC')),
            $completed,
            $totalRows,
            $processedRows,
            $downloadSize
        );
    }

    /**
     * Fetches report contents.
     *
     * @param int|string $reportIdentifier
     *
     * @return resource
     * @throws ExceptionInterface
     * @throws ApiAdapterException
     */
    #[\Override]
    public function fetchReportContents(int|string $reportIdentifier)
    {
        $endpoint = "api/report/$reportIdentifier/download";
        $response = $this->apiService->get($endpoint, ApiService::SCOPE_REPORT);
        if (is_array($response)) {
            $msg = isset($response['message']) && is_string($response['message']) ? $response['message'] : '';
            $code = isset($response['code']) && (is_int($response['code']) || is_numeric($response['code'])) ? (int)$response['code'] : 0;
            throw new ApiAdapterException($msg, $code);
        }

        $file = fopen('php://temp', 'w+');
        if ($file === false) {
            throw new ApiAdapterException('Could not open temporary stream');
        }

        $content = is_scalar($response) ? (string) $response : '';
        fwrite($file, $content);
        rewind($file);

        return $file;
    }

    /**
     * Validate an API response and assert that it is an array for Psalm.
     *
     * @psalm-assert array<string,mixed> $response
     */
    protected function validateResponse(mixed $response): void
    {
        if (is_array($response) === false) {
            $msg = is_scalar($response) ? htmlentities((string) $response) : '';
            throw new ApiAdapterException('Received invalid response from API: ' . $msg);
        }
        if (array_key_exists('code', $response) && $response['code'] != '000') {
            $msg = isset($response['message']) && is_string($response['message']) ? $response['message'] : '';
            $code = isset($response['code']) && (is_int($response['code']) || is_numeric($response['code'])) ? (int)$response['code'] : 0;
            throw new ApiAdapterException($msg, $code);
        }
    }

    private function formatReportCode(string|int $code, bool $stripPrefix = false): string
    {
        $reportCode = (string) $code;

        if ($stripPrefix) {
            if (str_starts_with($reportCode, self::REFERENCE_PREFIX)) {
                return substr($reportCode, strlen(self::REFERENCE_PREFIX));
            }

            return $reportCode;
        }

        return self::REFERENCE_PREFIX . $reportCode;
    }
}
