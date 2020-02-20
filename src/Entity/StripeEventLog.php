<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StripeEventLogRepository")
 */
class StripeEventLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeEventId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $handledAt;

    public function __construct($stripeEventId)
    {
        $this->stripeEventId = $stripeEventId;
        $this->handledAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeEventId(): ?string
    {
        return $this->stripeEventId;
    }

    public function setStripeEventId(string $stripeEventId): self
    {
        $this->stripeEventId = $stripeEventId;

        return $this;
    }

    public function getHandledAt(): ?\DateTimeInterface
    {
        return $this->handledAt;
    }

    public function setHandledAt(\DateTimeInterface $handledAt): self
    {
        $this->handledAt = $handledAt;

        return $this;
    }
}
