<?php

declare(strict_types=1);

namespace SharedReportTest\Service;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SharedReport\Adapter\ReportServiceAdapterInterface;
use SharedReport\Adapter\Request\ReportSpecification;
use SharedReport\Adapter\Response\AvailableReports;
use SharedReport\Adapter\Response\ReportStatus as ReportStatusResponse;
use SharedReport\Entity\AvailableReport;
use SharedReport\Entity\ReportStatus;
use SharedReport\Service\FileHandler\PassThrough;
use SharedReport\Service\ReportService;

/**
 * Class ReportServiceTest
 *
 * @package SharedReportTest\Service
 */
final class ReportServiceTest extends TestCase
{
    protected MockObject&ReportServiceAdapterInterface $stubAdapter;
    protected ?ReportService $service = null;

    #[\Override]
    public function setUp(): void
    {
        $this->stubAdapter = $this->getMockBuilder(ReportServiceAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new ReportService($this->stubAdapter);
    }

    public function testGetAvailableReportsCanHandleNoResults(): void
    {
        $this->stubAdapter->expects($this->once())
            ->method('fetchAvailableReports')
            ->willReturn(new AvailableReports(true));

        $this->assertSame([], $this->service?->getAvailableReports());
    }

    public function testFetchAvailableReports(): void
    {
        $dummyReports = new AvailableReports(true);
        $dummyReports->addReport('test', 'asd', 'a description');
        $dummyReports->addReport('test 2', 'asd2', 'a description');

        $this->stubAdapter->expects($this->once())
            ->method('fetchAvailableReports')
            ->willReturn($dummyReports);

        $reports = $this->service?->getAvailableReports();
        $this->assertEquals($dummyReports->getAvailableReports(), $reports);
    }

    /**
     * @throws Exception
     */
    public function testCreateReportSuccessReturnsStatus(): void
    {
        $spec     = new ReportSpecification('a1b2c3d4', 'csv');
        $fromDate = new DateTimeImmutable('-2 day', new \DateTimeZone('UTC'));
        $toDate   = new DateTimeImmutable('-1 day', new \DateTimeZone('UTC'));
        $spec->setDateFilters($fromDate->setTime(0, 0), $toDate->setTime(23, 59, 59));
        $date           = new DateTimeImmutable();
        $clientResponse = new ReportStatusResponse(
            true,
            'a1b2c3d4',
            'type-name',
            'type-code',
            $date,
            $date,
            false,
            0,
            2,
            0
        );
        $this->stubAdapter->expects($this->once())
            ->method('createReportRequest')
            ->with($spec)
            ->willReturn($clientResponse);

        $reportStatus = new ReportStatus(
            'a1b2c3d4',
            new AvailableReport('type-name', 'type-code'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            0,
            2,
            false,
            0
        );
        $result = $this->service?->createReport(
            'a1b2c3d4',
            'csv',
            $fromDate->format('c'),
            $toDate->format('c')
        );

        $this->assertNotNull($result);
        $this->assertEquals($reportStatus->getDownloadFileSize(), $result->getDownloadFileSize());
        $this->assertEquals($this->dateFormat($reportStatus->getToDateFilter()), $this->dateFormat($result->getToDateFilter()));
        $this->assertEquals($reportStatus->getProcessedRowCount(), $result->getProcessedRowCount());
        $this->assertEquals($reportStatus->getReference(), $result->getReference());
        $this->assertEquals($reportStatus->getReportTypeCode(), $result->getReportTypeCode());
        $this->assertEquals($reportStatus->getReportTypeName(), $result->getReportTypeName());
    }

    /**
     * @throws Exception
     */
    public function testCreateReportFiltersToDateToNeverBeAfterNow(): void
    {
        $spec     = new ReportSpecification('a1b2c3d4', 'csv');
        $fromDate = new DateTimeImmutable('-2 day', new \DateTimeZone('UTC'));
        $toDate   = new DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $spec->setDateFilters($fromDate->setTime(0, 0), $toDate);
        $clientResponse = new ReportStatusResponse(
            true,
            'a1b2c3d4',
            'type',
            'type-code',
            $fromDate,
            $toDate,
            false,
            0,
            2,
            0
        );
        $this->stubAdapter->expects($this->once())
            ->method('createReportRequest')
            ->willReturn($clientResponse);

        $availableReport = new AvailableReport('type', 'type-code');
        $status          = new ReportStatus(
            'a1b2c3d4',
            $availableReport,
            $fromDate,
            $toDate,
            0,
            2,
            false,
            0
        );
        $futureToDate    = $fromDate->setTime(23, 59, 59);
        $result          = $this->service?->createReport(
            'a1b2c3d4',
            'csv',
            $futureToDate->format('c'),
            $toDate->format('c')
        );

        $this->assertEquals($status, $result);
    }

    public function testGetStatusReturnsStatus(): void
    {
        $reference      = 'a1b2c3d4';
        $date           = new DateTimeImmutable('-143 seconds');
        $secondDate     = new DateTimeImmutable('+1 month');
        $clientResponse = new ReportStatusResponse(
            true,
            $reference,
            'type',
            'type-code',
            $date,
            $secondDate,
            false,
            2,
            0,
            2034
        );
        $this->stubAdapter->expects($this->once())
            ->method('fetchReportStatus')
            ->with($reference)
            ->willReturn($clientResponse);

        $result = $this->service?->getStatus($reference);
        $this->assertInstanceOf(ReportStatus::class, $result);
        $this->assertEquals(0, $result->getProcessedRowCount());
        $this->assertEquals(2, $result->getTotalRowCount());
        $this->assertEquals(false, $result->isComplete());
        $this->assertEquals('type-code', $result->getReportTypeCode());
        $this->assertEquals($date, $result->getFromDateFilter());
        $this->assertEquals($secondDate, $result->getToDateFilter());
        $this->assertEquals($reference, $result->getReference());
        $this->assertEquals(2034, $result->getDownloadFileSize());
    }

    public function testGetReportContentsThrowsExceptionIfNoFileHandlerIsSet(): void
    {
        $reportReference = 'a1b2c3d4';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("File Handler strategy not defined.");
        $contents = $this->service?->getOutputFile($reportReference);
        $this->assertIsResource($contents);
    }

    public function testGetReportContentsUsesFileHandler(): void
    {
        $reportReference = 'a1b2c3d4';
        $this->stubAdapter->expects($this->once())
            ->method('fetchReportContents')
            ->with($this->identicalTo($reportReference))
            ->willReturn(fopen('php://temp', 'w+'));
        $this->service?->setFileHandler(new PassThrough());
        $contents = $this->service?->getOutputFile($reportReference);
        $this->assertIsResource($contents);
    }

    private function dateFormat(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
