<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Follow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $follower;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followed")
     * @ORM\JoinColumn(nullable=false)
     */
    private $followed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function setFollower(?User $follower): self
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowed(): ?User
    {
        return $this->followed;
    }

    public function setFollowed(?User $followed): self
    {
        $this->followed = $followed;

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
