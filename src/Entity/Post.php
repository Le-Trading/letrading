<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="post")
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostVote", mappedBy="post")
     */
    private $postVotes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->postVotes = new ArrayCollection();
    }

    /**
     * Callback appelé à chaque création de post
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist(){
        if (empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }
        if (empty($this->isAdmin)){
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
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setPost($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getPost() === $this) {
                $file->setPost(null);
            }
        }

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
}
