<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact{

    /**
     * @var string|null
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom")
     * @Assert\Length(min=2, max=100)
     */
    private $firstName;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Vous devez renseigner votre nom")
     * @Assert\Length(min=2, max=100)
     */
    private $lastName;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Vous devez renseigner votre email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Vous devez renseigner un message")
     * @Assert\Length(min=10, max=500)
     */
    private $message;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}