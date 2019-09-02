<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Accessor(getter="getId")
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Exclude()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Secret", mappedBy="owner", orphanRemoval=true)
     * @Serializer\Exclude()
     */
    private $secrets;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Exclude()
     */
    private $emailValidationDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Exclude()
     */
    private $emailValidationMailSentDate;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Exclude()
     */
    private $emailValidationCode;


    public function __construct()
    {
        $this->secrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return array
     * @Serializer\VirtualProperty(name="attributes")
     */
    public function getAttributes()
    {
        return [
            'email' => $this->getEmail(),
            'emailValidationDate' => $this->getEmailValidationDate(),
        ];
    }

    /**
     * @return string
     * @Serializer\VirtualProperty(name="type")
     */
    public function getType()
    {
        return 'users';
    }

    /**
     * @return Collection|Secret[]
     */
    public function getSecrets(): Collection
    {
        return $this->secrets;
    }

    public function addSecret(Secret $secret): self
    {
        if (!$this->secrets->contains($secret)) {
            $this->secrets[] = $secret;
            $secret->setOwner($this);
        }

        return $this;
    }

    public function removeSecret(Secret $secret): self
    {
        if ($this->secrets->contains($secret)) {
            $this->secrets->removeElement($secret);
            // set the owning side to null (unless already changed)
            if ($secret->getOwner() === $this) {
                $secret->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEmailValidationDate(): ?\DateTime
    {
        return $this->emailValidationDate;
    }

    /**
     * @param \DateTime $emailValidationDate
     *
     * @return User
     */
    public function setEmailValidationDate(?\DateTime $emailValidationDate): User
    {
        $this->emailValidationDate = $emailValidationDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEmailValidationMailSentDate(): ?\DateTime
    {
        return $this->emailValidationMailSentDate;
    }

    /**
     * @param \DateTime $emailValidationMailSentDate
     *
     * @return User
     */
    public function setEmailValidationMailSentDate(?\DateTime $emailValidationMailSentDate): User
    {
        $this->emailValidationMailSentDate = $emailValidationMailSentDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailValidationCode(): ?string
    {
        return $this->emailValidationCode;
    }

    /**
     * @param string $emailValidationCode
     *
     * @return User
     */
    public function setEmailValidationCode(?string $emailValidationCode): User
    {
        $this->emailValidationCode = $emailValidationCode;

        return $this;
    }
}
