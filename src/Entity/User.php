<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="8",
     *     max="4096"
     * )
     */
    private $password;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Secret", mappedBy="owner", orphanRemoval=true)
     */
    private $secrets;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="user")
     */
    private $logs;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $emailAddressValidated;

    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="App\Entity\Organization", mappedBy="members")
     */
    private $organizations;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Organization", mappedBy="owner")
     */
    private $ownedOrganizations;

    public function __construct()
    {
        $this->secrets = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->emailAddressValidated = false;
        $this->organizations = new ArrayCollection();
        $this->ownedOrganizations = new ArrayCollection();
    }

    public function getId()
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
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailAddressValidated(): bool
    {
        return $this->emailAddressValidated;
    }

    /**
     * @param bool $emailAddressValidated
     *
     * @return User
     */
    public function setEmailAddressValidated(bool $emailAddressValidated): User
    {
        $this->emailAddressValidated = $emailAddressValidated;

        return $this;
    }

    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(Organization $organization): self
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations[] = $organization;
        }
        return $this;
    }

    public function removeOrganization(Organization $organization): self
    {
        if ($this->organizations->contains($organization)) {
            $this->organizations->removeElement($organization);
        }
        return $this;
    }

    public function getOwnedOrganizations(): Collection
    {
        return $this->ownedOrganizations;
    }

    public function addOwnedOrganization(Organization $organization): self
    {
        if (!$this->ownedOrganizations->contains($organization)) {
            $this->ownedOrganizations[] = $organization;
        }
        return $this;
    }

    public function removeOwnedOrganization(Organization $organization): self
    {
        if ($this->ownedOrganizations->contains($organization)) {
            $this->ownedOrganizations->removeElement($organization);
        }
        return $this;
    }

}
