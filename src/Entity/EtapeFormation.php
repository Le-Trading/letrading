<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtapeFormationRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"title"},
 *     message="L'étape existe déja. Merci de choisir un autre nom"
 * )
 */
class EtapeFormation
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SectionFormation", inversedBy="etapeFormations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", mappedBy="etapeFormation", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", mappedBy="etapeContenuFormation", cascade={"persist", "remove"})
     */
    private $content;

    /**
     * Permet d'init le la date de creation
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
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

    public function getSection(): ?SectionFormation
    {
        return $this->section;
    }

    public function setSection(?SectionFormation $section): self
    {
        $this->section = $section;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        // set (or unset) the owning side of the relation if necessary
        $newEtapeFormation = null === $media ? null : $this;
        if ($media->getEtapeFormation() !== $newEtapeFormation) {
            $media->setEtapeFormation($newEtapeFormation);
        }
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getContent(): ?Media
    {
        return $this->content;
    }

    public function setContent(?Media $content): self
    {
        $this->content = $content;

        // set (or unset) the owning side of the relation if necessary
        $newEtapeContenuFormation = null === $content ? null : $this;
        if ($content->getEtapeContenuFormation() !== $newEtapeContenuFormation) {
            $content->setEtapeContenuFormation($newEtapeContenuFormation);
        }
        return $this;
    }
}
