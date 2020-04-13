<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OffersRepository")
 */
class Offers
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="Offer")
     */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Souscription", mappedBy="offer")
     */
    private $souscriptions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", inversedBy="offers", cascade={"persist", "remove"})
     */
    private $media;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->souscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setOffer($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getOffer() === $this) {
                $payment->setOffer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Souscription[]
     */
    public function getSouscriptions(): Collection
    {
        return $this->souscriptions;
    }

    public function addSouscription(Souscription $souscription): self
    {
        if (!$this->souscriptions->contains($souscription)) {
            $this->souscriptions[] = $souscription;
            $souscription->setOffer($this);
        }

        return $this;
    }

    public function removeSouscription(Souscription $souscription): self
    {
        if ($this->souscriptions->contains($souscription)) {
            $this->souscriptions->removeElement($souscription);
            // set the owning side to null (unless already changed)
            if ($souscription->getOffer() === $this) {
                $souscription->setOffer(null);
            }
        }

        return $this;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}
