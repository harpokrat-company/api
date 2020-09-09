<?php


namespace App\Entity\VaultOwnership;

use App\Entity\Vault;
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
 *     "group" = "OrganizationGroupVaultOwnership",
 *     "user" = "UserVaultOwnership",
 * })
 */
abstract class VaultOwnership
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
     * @ORM\OneToMany(targetEntity="App\Entity\Vault", mappedBy="owner", cascade={"persist", "remove"})
     */
    private $vaults;

    public function __construct()
    {
        $this->vaults = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVaults(): Collection
    {
        return $this->vaults;
    }

    public function addVault(Vault $vault): self
    {
        if (!$this->vaults->contains($vault)) {
            $this->vaults[] = $vault;
        }
        return $this;
    }

    public function removeVault(Vault $vault): self
    {
        if ($this->vaults->contains($vault)) {
            $this->vaults->removeElement($vault);
        }
        return $this;
    }

    abstract function getOwner(): ?VaultOwnerInterface;

    abstract function setOwner(?VaultOwnerInterface $owner): self;
}

