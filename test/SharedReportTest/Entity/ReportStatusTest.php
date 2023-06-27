<?php

namespace SharedReportTest\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SharedReport\Entity\AvailableReport;
use SharedReport\Entity\ReportStatus;

/**
 * Class ReportStatusTest
 *
 * @package SharedReportTest\Entity
 */
class ReportStatusTest extends TestCase
{
    public function testIsReadyForDownloadIsIndependentOfIsComplete()
    {
        $reportDetails = new AvailableReport('type', '1234');
        $status        = new ReportStatus(
            'asd', $reportDetails, new DateTimeImmutable(), new DateTimeImmutable(), 1, 1, false, 0
        );
        $this->assertFalse($status->isReadyForDownload());
        $status = new ReportStatus(
            'asd', $reportDetails, new DateTimeImmutable(), new DateTimeImmutable(), 1, 0, true, 0
        );
        $this->assertTrue($status->isReadyForDownload());
    }

    public function testGetters()
    {
        $status = new ReportStatus(
            'asd',
            new AvailableReport('type', '1234'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1,
            1,
            false,
            0
        );
        $this->assertEquals('type', $status->getReportTypeName());
    }

    /**
     * @dataProvider rowCountsProvider
     *
     * @param $processedRows
     * @param $totalRows
     */
    public function testCalculatesPercentageCompleteCorrectly($processedRows, $totalRows)
    {
        $status = new ReportStatus(
            'test',
            new AvailableReport('type', '1234'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            $totalRows,
            $processedRows,
            false,
            0
        );
        if ($processedRows == 0 && $totalRows == 0) {
            $expected = 100;
        } elseif ($processedRows <= 0) {
            $expected = 0;
        } else {
            $expected = (int)round(100 * $processedRows / $totalRows);
        }
        $this->assertSame($expected, $status->getPercentComplete());
    }

    public function rowCountsProvider()
    {
        return [
            [0, 0],
            [0, 1],
            [1, 1],
            [2, 2],
            [0, 3],
            [1, 3],
        ];
    }
}
