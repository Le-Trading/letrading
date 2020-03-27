<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Media implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="media", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Formation", inversedBy="media", cascade={"persist", "remove"})
     */
    private $formation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SectionFormation", inversedBy="media", cascade={"persist", "remove"})
     */
    private $sectionFormation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\EtapeFormation", inversedBy="media", cascade={"persist", "remove"})
     */
    private $etapeFormation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\EtapeFormation", inversedBy="media", cascade={"persist", "remove"})
     */
    private $etapeContenuFormation;

    /**
     * @Vich\UploadableField(mapping="default", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $defaultFile;

    /**
     * @Vich\UploadableField(mapping="avatar", fileNameProperty="imageName", size="imageSize")
     * 
     * @var File
     */
    private $avatarFile;

    /**
     * @Vich\UploadableField(mapping="forum", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $forumFile;

    /**
     * @Vich\UploadableField(mapping="formation", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $formationFile;

    /**
     * @Vich\UploadableField(mapping="section_formation", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $sectionFormationFile;

    /**
     * @Vich\UploadableField(mapping="etape_formation", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $etapeFormationFile;

    /**
     * @Vich\UploadableField(mapping="etape_contenu_formation", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $etapeContenuFormationFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Post", inversedBy="media", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * Permet d'init le slug
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist()
    {
        if (empty($this->updatedAt)) {
            $this->updatedAt = new \DateTime();
        }
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

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getSectionFormation(): ?SectionFormation
    {
        return $this->sectionFormation;
    }

    public function setSectionFormation(?SectionFormation $sectionFormation): self
    {
        $this->sectionFormation = $sectionFormation;

        return $this;
    }

    public function getEtapeFormation(): ?EtapeFormation
{
    return $this->etapeFormation;
}

    public function setEtapeFormation(?EtapeFormation $etapeFormation): self
    {
        $this->etapeFormation = $etapeFormation;

        return $this;
    }

    public function getEtapeContenuFormation(): ?EtapeFormation
    {
        return $this->etapeContenuFormation;
    }

    public function setEtapeContenuFormation(?EtapeFormation $etapeContenuFormation): self
    {
        $this->etapeContenuFormation = $etapeContenuFormation;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $defaultFile
     */
    public function setDefaultFile(?File $defaultFile = null): void
    {
        $this->defaultFile = $defaultFile;

        if (null !== $defaultFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getDefaultFile(): ?File
    {
        return $this->defaultFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $forumFile
     */
    public function setForumFile(?File $forumFile = null): void
    {
        $this->forumFile = $forumFile;

        if (null !== $forumFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getForumFile(): ?File
    {
        return $this->forumFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $formationFile
     */
    public function setFormationFile(?File $formationFile = null): void
    {
        $this->formationFile = $formationFile;

        if (null !== $formationFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFormationFile(): ?File
    {
        return $this->formationFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $sectionFormationFile
     */
    public function setSectionFormationFile(?File $sectionFormationFile = null): void
    {
        $this->sectionFormationFile = $sectionFormationFile;

        if (null !== $sectionFormationFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getSectionFormationFile(): ?File
    {
        return $this->sectionFormationFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $etapeFormationFile
     */
    public function setEtapeFormationFile(?File $etapeFormationFile = null): void
    {
        $this->etapeFormationFile = $etapeFormationFile;

        if (null !== $etapeFormationFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getEtapeFormationFile(): ?File
    {
        return $this->etapeFormationFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $etapeContenuFormationFile
     */
    public function setEtapeContenuFormationFile(?File $etapeContenuFormationFile = null): void
    {
        $this->etapeContenuFormationFile = $etapeContenuFormationFile;

        if (null !== $etapeContenuFormationFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getEtapeContenuFormationFile(): ?File
    {
        return $this->etapeContenuFormationFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->imageName,
        ));
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->imageName,
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
