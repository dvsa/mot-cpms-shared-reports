<?php

namespace SharedReport\Entity;

/**
 * Class AvailableReport
 *
 * @package SharedReport\Entity
 */
final class AvailableReport
{
    public function __construct(
        protected string $name,
        protected string $reference,
        protected string $description = ''
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }
}
