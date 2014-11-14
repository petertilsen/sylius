<?php

namespace GGG\Bundle\BpmnBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bpmn
 */
class Bpmn
{
    /**
     * @var \DateTime
     */
    private $expiresAt;


    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return Bpmn
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
