<?php

declare(strict_types=1);

namespace SharedReport\Adapter\Response;

use SharedReport\Entity\AvailableReport;

/**
 * Class AvailableReports
 *
 * @package SharedReport\Adapter\Response
 */
final class AvailableReports extends AbstractResponse
{
    protected bool $success;
    /**
     * @var AvailableReport[]
     */
    private array $availableReports = [];

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function addReport(string $name, string $reference, string $description = ''): void
    {
        $this->availableReports[] = new AvailableReport($name, $reference, $description);
    }

    /**
     * @return AvailableReport[]
     */
    public function getAvailableReports(): array
    {
        return $this->availableReports;
    }
}
