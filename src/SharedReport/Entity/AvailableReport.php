<?php

namespace SharedReport\Entity;

/**
 * Class AvailableReport
 *
 * @package SharedReport\Entity
 */
class AvailableReport
{
    /** @var  string Name of the report */
    protected $name;
    /** @var  string */
    protected $description;
    /** @var  string The code/reference used for this report on the remote service */
    protected $reference;

    /**
     * @param        $name
     * @param        $reference
     * @param string $description
     */
    public function __construct($name, $reference, $description = '')
    {
        $this->name        = $name;
        $this->reference   = $reference;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
