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
     * @ORM\ManyToOne(targetEntity="App\Entity\Offers", inversedBy="souscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offer;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $billingPeriodEndsAt;

    /**
     * @ORM\Column(type="string")
     */
    private $stripeSubscriptionId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="souscription", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endsAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEnded;

    public function getStripeSubscriptionId()
    {
        return $this->stripeSubscriptionId;
    }


    public function setStripeSubscriptionId(string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
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


    /**
     * @return \DateTime
     */
    public function getBillingPeriodEndsAt()
    {
        return $this->billingPeriodEndsAt;
    }

    public function setBillingPeriodEndsAt(\DateTimeInterface $billingPeriodEndsAt){
        $this->billingPeriodEndsAt = $billingPeriodEndsAt;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }
    public function setEndsAt(\DateTime $endsAt = null)
    {
        $this->endsAt = $endsAt;
    }

    /***********/
    /*********** GESTION DE L'ABONNEMENT *************/

    /******
     * @param Offers $offer
     * @param $stripeSubscriptionId
     * @param \DateTime $periodEnd
     */
    public function activateSubscription(Offers $offer, $stripeSubscriptionId, \DateTime $periodEnd)
    {
        $this->offer = $offer;
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        $this->billingPeriodEndsAt = $periodEnd;
        $this->endsAt = null;
        $this->isEnded = false;
    }

    public function desactivateSubscription()
    {
        // paid through end of period
        $this->endsAt = $this->billingPeriodEndsAt;
        $this->billingPeriodEndsAt = null;
        $this->isEnded = true;
    }

    public function isActive()
    {
        return ($this->endsAt === null || $this->endsAt > new \DateTime()) && !$this->isEnded == true;
    }
    public function isCancelled()
    {
        return $this->endsAt !== null && $this->isEnded !== true;
    }

    public function getIsEnded(): ?bool
    {
        return $this->isEnded;
    }

    public function setIsEnded(?bool $isEnded): self
    {
        $this->isEnded = $isEnded;

        return $this;
    }

}
