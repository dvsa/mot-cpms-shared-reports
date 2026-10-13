<?php

declare(strict_types=1);

namespace SharedReportTest\Adapter;

use CpmsClient\Service\ApiService;
use Laminas\Cache\Exception\ExceptionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\ApiAdapterException;
use SharedReport\Adapter\CpmsRest;
use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\AvailableReports;
use SharedReport\Adapter\Response\ReportStatus;

/**
 * Class CpmsRestTest
 *
 * @package SharedReportTest\Adapter
 */
final class CpmsRestTest extends TestCase
{
    protected CpmsRest $client;
    protected ApiService&MockObject $apiServiceMock;

    #[\Override]
    public function setUp(): void
    {
        $this->apiServiceMock = $this->createMock(ApiService::class);
        $this->client = new CpmsRest($this->apiServiceMock);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testThrowsExceptionWhenReceivingNonArrayResponses(): void
    {
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with('api/report', ApiService::SCOPE_REPORT)
            ->willReturn('response');

        $this->expectException(ApiAdapterException::class);
        $this->expectExceptionMessage("Received invalid response from API: response");
        $this->client->fetchAvailableReports();
    }

    /**
     * @throws ExceptionInterface
     */
    public function testThrowsExceptionWhenReceivingErrorResponse(): void
    {
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with('api/report', ApiService::SCOPE_REPORT)
            ->willReturn(
                [
                    'code'    => 777,
                    'message' => 'error message here'
                ]
            );

        $this->expectException(ApiAdapterException::class);
        $this->expectExceptionMessage("error message here");
        $this->expectExceptionCode(777);
        $this->client->fetchAvailableReports();
    }

    /**
     * @throws ExceptionInterface
     */
    public function testFetchAvailableReports(): void
    {
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with('api/report', ApiService::SCOPE_REPORT)
            ->willReturn(
                ['code'  => '000',
                 'items' => [
                     ['name' => 'asd', 'code' => '123', 'description' => 'description']
                 ]
                ]
            );
        $reports = $this->client->fetchAvailableReports();
        $this->assertInstanceOf(AvailableReports::class, $reports);
        $this->assertCount(1, $reports->getAvailableReports());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testCreateReportRequest(): void
    {
        $spec = new ReportSpecification('asd');

        $this->apiServiceMock->expects($this->once())
            ->method('post')
            ->with(
                'api/report',
                ApiService::SCOPE_REPORT,
                [
                    'report_code' => 'asd',
                    'filters'     => [
                        'from' => $spec->getFromDateFilter()->format('Y-m-d H:i:s'),
                        'to'   => $spec->getToDateFilter()->format('Y-m-d H:i:s')
                    ],
                    'format'      => 'csv']
            )
            ->willReturn(
                [
                    'code'       => '000',
                    'reference'  => '123',
                    'total_rows' => 0,
                ]
            );
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->willReturn(
                [
                    'code'             => '000',
                    'reference'        => '123',
                    'total_rows'       => 0,
                    'report_type'      => 'sad',
                    'report_type_code' => 'sad',
                    'download_size'    => 0,
                    'report_filters'   => [
                        'from' => date('c'),
                        'to'   => date('c')
                    ],
                    'completed'        => false,
                    'processed_rows'   => 0
                ]
            );
        $response = $this->client->createReportRequest($spec);
        $this->assertInstanceOf(ReportStatus::class, $response);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testFetchReportContents(): void
    {
        $fileContent      = uniqid('file contents');
        $reportIdentifier = uniqid('ref');
        $url              = 'api/report/' . $reportIdentifier . '/download';
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with($url, ApiService::SCOPE_REPORT)
            ->willReturn($fileContent);
        $file = $this->client->fetchReportContents($reportIdentifier);
        $this->assertIsResource($file);
        $this->assertEquals($fileContent, fgets($file));
    }

    /**
     * @throws ExceptionInterface
     */
    public function testFetchReportContentsWithError(): void
    {
        $reportIdentifier = uniqid('ref');
        $url              = 'api/report/' . $reportIdentifier . '/download';
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with($url, ApiService::SCOPE_REPORT)
            ->willReturn(['code' => '512', 'message' => 'a message']);

        $this->expectException(ApiAdapterException::class);
        $this->expectExceptionMessage("a message");
        $this->expectExceptionCode(512);
        $this->client->fetchReportContents($reportIdentifier);
    }
}
