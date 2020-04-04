<?php

namespace App\Entity;

use Serializable;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields={"email"},
 *  message="L'email est déja utilisé, merci de le modifier"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre nom")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre pseudo")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide !")
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Mot de passe invalide")
     */
    private $hash;

    /**
     * @Assert\EqualTo(propertyPath="hash", message="Mot de passe invalide")
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostVote", mappedBy="user", orphanRemoval=true)
     */
    private $postVotes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="user")
     */
    private $userRole;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", mappedBy="user", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="User")
     */
    private $payments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $stripeCustomerId;

    /**
     * @return mixed
     */
    public function getStripeCustomerId()
    {
        return $this->stripeCustomerId;
    }

    /**
     * @param mixed $stripeCustomerId
     */
    public function setStripeCustomerId($stripeCustomerId): void
    {
        $this->stripeCustomerId = $stripeCustomerId;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->postVotes = new ArrayCollection();
        $this->userRole = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->userReceiverNotif = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->followed = new ArrayCollection();
        $this->temoignages = new ArrayCollection();
    }

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
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstName . ' ' . $this->lastName);
        }
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getFullName()
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }
    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }
        return $this;
    }


    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
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
            $postVote->setUser($this);
        }

        return $this;
    }

    public function removePostVote(PostVote $postVote): self
    {
        if ($this->postVotes->contains($postVote)) {
            $this->postVotes->removeElement($postVote);
            // set the owning side to null (unless already changed)
            if ($postVote->getUser() === $this) {
                $postVote->setUser(null);
            }
        }
        return $this;
    }

    public function getRoles()
    {
        $roles = $this->userRole->map(function ($role) {
            return $role->getTitle();
        })->toArray();
        $roles[] = 'ROLE_USER';
        return $roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->userRole->contains($role)) {
            $this->userRole[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->userRole->contains($role)) {
            $this->userRole->removeElement($role);
            $role->removeUser($this);
        }

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
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

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
        $newUser = null === $media ? null : $this;
        if ($media->getUser() !== $newUser) {
            $media->setUser($newUser);
        }

        return $this;
    }
    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Souscription", mappedBy="user", cascade={"persist", "remove"})
     */
    private $souscription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notif", mappedBy="receiver", orphanRemoval=true)
     */
    private $userReceiverNotif;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Conversation", mappedBy="participants")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    private $conversations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Follow", mappedBy="follower", orphanRemoval=true)
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Follow", mappedBy="followed")
     */
    private $followed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Temoignage", mappedBy="author", orphanRemoval=true)
     */
    private $temoignages;

    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
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
     * @return Collection|Notif[]
     */
    public function getUserReceiverNotif(): Collection
    {
        return $this->userReceiverNotif;
    }

    public function addUserReceiverNotif(Notif $userReceiverNotif): self
    {
        if (!$this->userReceiverNotif->contains($userReceiverNotif)) {
            $this->userReceiverNotif[] = $userReceiverNotif;
            $userReceiverNotif->setReceiver($this);
        }

        return $this;
    }

    public function removeUserReceiverNotif(Notif $userReceiverNotif): self
    {
        if ($this->userReceiverNotif->contains($userReceiverNotif)) {
            $this->userReceiverNotif->removeElement($userReceiverNotif);
            // set the owning side to null (unless already changed)
            if ($userReceiverNotif->getReceiver() === $this) {
                $userReceiverNotif->setReceiver(null);
            }
        }

        return $this;
    }

    public function getSouscription(): ?Souscription
    {
        return $this->souscription;
    }

    public function setSouscription(Souscription $souscription): self
    {
        $this->souscription = $souscription;

        // set the owning side of the relation if necessary
        if ($souscription->getUser() !== $this) {
            $souscription->setUser($this);
        }

        return $this;
    }

    public function hasActiveSubscription()
    {
        return $this->getSouscription() && $this->getSouscription()->isActive();
    }

    public function hasActiveNonCancelledSubscription()
    {
        return $this->hasActiveSubscription() && !$this->getSouscription()->isCancelled();
    }

    public function ownThisOffer(Offers $offer){
        if($offer->getType() == "subscription" && $this->hasActiveSubscription()){
            return true;
        }
        foreach ($this->getPayments() as $payment ){
            if ($payment->getOffer() === $offer)
                return true;
        }
        return false;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addParticipants($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->contains($conversation)) {
            $this->conversations->removeElement($conversation);
            $conversation->removeParticipants($this);
        }

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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Follow[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Follow $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
            $follower->setFollower($this);
        }

        return $this;
    }

    public function removeFollower(Follow $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
            // set the owning side to null (unless already changed)
            if ($follower->getFollower() === $this) {
                $follower->setFollower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Follow[]
     */
    public function getFollowed(): Collection
    {
        return $this->followed;
    }

    public function addFollowed(Follow $followed): self
    {
        if (!$this->followed->contains($followed)) {
            $this->followed[] = $followed;
            $followed->setFollowed($this);
        }

        return $this;
    }

    public function removeFollowed(Follow $followed): self
    {
        if ($this->followed->contains($followed)) {
            $this->followed->removeElement($followed);
            // set the owning side to null (unless already changed)
            if ($followed->getFollowed() === $this) {
                $followed->setFollowed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Temoignage[]
     */
    public function getTemoignages(): Collection
    {
        return $this->temoignages;
    }

    public function addTemoignage(Temoignage $temoignage): self
    {
        if (!$this->temoignages->contains($temoignage)) {
            $this->temoignages[] = $temoignage;
            $temoignage->setAuthor($this);
        }

        return $this;
    }

    public function removeTemoignage(Temoignage $temoignage): self
    {
        if ($this->temoignages->contains($temoignage)) {
            $this->temoignages->removeElement($temoignage);
            // set the owning side to null (unless already changed)
            if ($temoignage->getAuthor() === $this) {
                $temoignage->setAuthor(null);
            }
        }

        return $this;
    }

}
