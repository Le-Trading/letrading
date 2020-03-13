<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionFormationRepository")
 */
class SectionFormation
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EtapeFormation", mappedBy="section", orphanRemoval=true)
     */
    private $etapeFormations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formation", inversedBy="sectionFormations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->etapeFormations = new ArrayCollection();
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|EtapeFormation[]
     */
    public function getEtapeFormations(): Collection
    {
        return $this->etapeFormations;
    }

    public function addEtapeFormation(EtapeFormation $etapeFormation): self
    {
        if (!$this->etapeFormations->contains($etapeFormation)) {
            $this->etapeFormations[] = $etapeFormation;
            $etapeFormation->setSection($this);
        }

        return $this;
    }

    public function removeEtapeFormation(EtapeFormation $etapeFormation): self
    {
        if ($this->etapeFormations->contains($etapeFormation)) {
            $this->etapeFormations->removeElement($etapeFormation);
            // set the owning side to null (unless already changed)
            if ($etapeFormation->getSection() === $this) {
                $etapeFormation->setSection(null);
            }
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
