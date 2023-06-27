<?php

namespace SharedReportTest\Client;

use CpmsClient\Service\ApiService;
use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\CpmsRest;
use SharedReport\Adapter\Request\ReportSpecification;

/**
 * Class CpmsRestTest
 *
 * @package SharedReportTest\Client
 */
class CpmsRestTest extends TestCase
{
    /** @var  CpmsRest */
    protected $client;
    /** @var ApiService|TestCase */
    protected $apiServiceMock;

    public function setUp(): void
    {
        $this->apiServiceMock = $this->getMockBuilder('CpmsClient\Service\ApiService')
            ->getMock();
        $this->client         = new CpmsRest($this->apiServiceMock);
    }

    public function testThrowsExceptionWhenReceivingNonArrayResponses()
    {
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with('api/report', ApiService::SCOPE_REPORT)
            ->willReturn('response');

        $this->expectException(\SharedReport\Adapter\ApiAdapterException::class);
        $this->expectExceptionMessage("Received invalid response from API: response");
        $this->client->fetchAvailableReports();
    }

    public function testThrowsExceptionWhenReceivingErrorResponse()
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

        $this->expectException(\SharedReport\Adapter\ApiAdapterException::class);
        $this->expectExceptionMessage("error message here");
        $this->expectExceptionCode(777);
        $this->client->fetchAvailableReports();
    }

    public function testFetchAvailableReports()
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
        $this->assertInstanceOf('SharedReport\Adapter\Response\AvailableReports', $reports);
        $this->assertCount(1, $reports->getAvailableReports());
    }

    public function testCreateReportRequest()
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
        $this->assertInstanceOf('SharedReport\Adapter\Response\ReportStatus', $response);
    }

    public function testFetchReportContents()
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

    public function testFetchReportContentsWithError()
    {
        $reportIdentifier = uniqid('ref');
        $url              = 'api/report/' . $reportIdentifier . '/download';
        $this->apiServiceMock->expects($this->once())
            ->method('get')
            ->with($url, ApiService::SCOPE_REPORT)
            ->willReturn(['code' => '512', 'message' => 'a message']);

        $this->expectException(\SharedReport\Adapter\ApiAdapterException::class);
        $this->expectExceptionMessage("a message");
        $this->expectExceptionCode(512);
        $this->client->fetchReportContents($reportIdentifier);
    }
}
