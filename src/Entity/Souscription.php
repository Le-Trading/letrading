<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SouscriptionRepository")
 */
class Souscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="souscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Offers", inversedBy="souscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $beginningAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endingAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cancelled;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOffer(): ?Offers
    {
        return $this->offer;
    }

    public function setOffer(?Offers $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getBeginningAt(): ?\DateTimeInterface
    {
        return $this->beginningAt;
    }

    public function setBeginningAt(\DateTimeInterface $beginningAt): self
    {
        $this->beginningAt = $beginningAt;

        return $this;
    }

    public function getEndingAt(): ?\DateTimeInterface
    {
        return $this->endingAt;
    }

    public function setEndingAt(\DateTimeInterface $endingAt): self
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(?bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }
}
