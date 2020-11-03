<?php

namespace App\Entity\SecretOwnership;

use App\Entity\Secret;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Ramsey\Uuid\UuidInterface;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "group" = "OrganizationGroupSecretOwnership",
 *     "user" = "UserSecretOwnership",
 *     "vault" = "VaultSecretOwnership",
 * })
 */
abstract class SecretOwnership
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
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Secret", mappedBy="owner")
     */
    private $secrets;

    public function __construct()
    {
        $this->secrets = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSecrets(): Collection
    {
        return $this->secrets;
    }

    public function addSecret(Secret $secret): self
    {
        if (!$this->secrets->contains($secret)) {
            $this->secrets[] = $secret;
        }

        return $this;
    }

    public function removeSecret(Secret $secret): self
    {
        if ($this->secrets->contains($secret)) {
            $this->secrets->removeElement($secret);
        }

        return $this;
    }

    abstract public function getOwner(): ?SecretOwnerInterface;

    abstract public function setOwner(?SecretOwnerInterface $owner): self;
}
