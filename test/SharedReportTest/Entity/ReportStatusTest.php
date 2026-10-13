<?php

declare(strict_types=1);

namespace SharedReportTest\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SharedReport\Entity\AvailableReport;
use SharedReport\Entity\ReportStatus;

/**
 * Class ReportStatusTest
 *
 * @package SharedReportTest\Entity
 */
final class ReportStatusTest extends TestCase
{
    public function testIsReadyForDownloadIsIndependentOfIsComplete(): void
    {
        $reportDetails = new AvailableReport('type', '1234');
        $status        = new ReportStatus(
            'asd',
            $reportDetails,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1,
            1,
            false,
            0
        );
        $this->assertFalse($status->isReadyForDownload());
        $status = new ReportStatus(
            'asd',
            $reportDetails,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1,
            0,
            true,
            0
        );
        $this->assertTrue($status->isReadyForDownload());
    }

    public function testGetters(): void
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
     * @param int $processedRows
     * @param int $totalRows
     */
    #[DataProvider('rowCountsProvider')]
    public function testCalculatesPercentageCompleteCorrectly(int $processedRows, int $totalRows): void
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

    /**
     * @return array<int, array<int, int>>
     */
    public static function rowCountsProvider(): array
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
