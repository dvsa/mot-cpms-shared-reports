<?php

namespace SharedReport\Entity;

/**
 * Class AvailableReport
 *
 * @package SharedReport\Entity
 */
final class AvailableReport
{
    /** @var  string Name of the report */
    protected string $name;
    /** @var  string */
    protected string $description;
    /** @var  string The code/reference used for this report on the remote service */
    protected string $reference;

    /**
     * @param string $name
     * @param string $reference
     * @param string $description
     */
    public function __construct(string $name, string $reference, string $description = '')
    {
        $this->name        = $name;
        $this->reference   = $reference;
        $this->description = $description;
    }

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
