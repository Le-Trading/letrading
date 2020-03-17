<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormationRepository")
 */
class Formation
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
     * @ORM\OneToMany(targetEntity="App\Entity\SectionFormation", mappedBy="formation", orphanRemoval=true)
     */
    private $sectionFormations;

    public function __construct()
    {
        $this->sectionFormations = new ArrayCollection();
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
     * @return Collection|SectionFormation[]
     */
    public function getSectionFormations(): Collection
    {
        return $this->sectionFormations;
    }

    public function addSectionFormation(SectionFormation $sectionFormation): self
    {
        if (!$this->sectionFormations->contains($sectionFormation)) {
            $this->sectionFormations[] = $sectionFormation;
            $sectionFormation->setFormation($this);
        }

        return $this;
    }

    public function removeSectionFormation(SectionFormation $sectionFormation): self
    {
        if ($this->sectionFormations->contains($sectionFormation)) {
            $this->sectionFormations->removeElement($sectionFormation);
            // set the owning side to null (unless already changed)
            if ($sectionFormation->getFormation() === $this) {
                $sectionFormation->setFormation(null);
            }
        }

        return $this;
    }
}