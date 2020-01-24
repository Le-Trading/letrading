<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Thread", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Thread;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     */
    private $content;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostVote", mappedBy="post")
     */
    private $postVotes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", mappedBy="post", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="responses")
     */
    private $respond;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="respond")
     */
    private $responses;


    public function __construct()
    {
        $this->postVotes = new ArrayCollection();
        $this->responses = new ArrayCollection();
    }


    /**
     * Callback appelé à chaque création de post
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if (empty($this->updatedAt)) {
            $this->updatedAt = new \DateTime();
        }
        if (empty($this->isAdmin)) {
            $this->isAdmin = false;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->Thread;
    }

    public function setThread(?Thread $Thread): self
    {
        $this->Thread = $Thread;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|PostVote[]
     */
    public function getPostVotes(): Collection
    {
        return $this->postVotes;
    }

    public function addPostVote(PostVote $postVote): self
    {
        if (!$this->postVotes->contains($postVote)) {
            $this->postVotes[] = $postVote;
            $postVote->setPost($this);
        }

        return $this;
    }

    public function removePostVote(PostVote $postVote): self
    {
        if ($this->postVotes->contains($postVote)) {
            $this->postVotes->removeElement($postVote);
            // set the owning side to null (unless already changed)
            if ($postVote->getPost() === $this) {
                $postVote->setPost(null);
            }
        }

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

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
        $newPost = null === $media ? null : $this;
        if ($media->getPost() !== $newPost) {
            $media->setPost($newPost);
        }

        return $this;
    }

    public function getRespond(): ?self
    {
        return $this->respond;
    }

    public function setRespond(?self $respond): self
    {
        $this->respond = $respond;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(self $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setRespond($this);
        }

        return $this;
    }

    public function removeResponse(self $response): self
    {
        if ($this->responses->contains($response)) {
            $this->responses->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getRespond() === $this) {
                $response->setRespond(null);
            }
        }

        return $this;
    }

    /**
     * Retourne vrai si le post a été voté par l'utilisateur passé en paramètre
     *
     * @param User $user
     * @return boolean
     */
    public function isLikedByUser(User $user): bool
    {
        foreach ($this->postVotes as $vote) {
            if ($vote->getUser() === $user)
                return true;
        }

        return false;
    }
}
