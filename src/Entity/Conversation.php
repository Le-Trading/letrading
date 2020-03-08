<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Conversation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="conversations")
     */
    private $participants;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="conversation", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * Callback appelé à chaque création de conversation
     *
     * @ORM\PrePersist
     *
     * @return void
     * @throws \Exception
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if(empty($this->title)){
            $titre = "Conversation entre ";
            $countParticipants = count($this->participants);
            $i = 0;
            foreach($this->participants as $participant){
                if(++$i != $countParticipants)
                    $titre .= $participant->getPseudo() . ", ";
                else{
                    $titre .= $participant->getPseudo();
                }
            }
            $this->title = $titre;
        }
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipants(User $participants): self
    {
        if (!$this->participants->contains($participants)) {
            $this->participants[] = $participants;
        }

        return $this;
    }

    public function removeParticipants(User $participants): self
    {
        if ($this->participants->contains($participants)) {
            $this->participants->removeElement($participants);
        }

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

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function hasUser(User $user)
    {
        foreach ($this->participants as $member) {
            if($member->getId() == $user->getId()) {
                return true;
            }
        }
        return false;
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
}
