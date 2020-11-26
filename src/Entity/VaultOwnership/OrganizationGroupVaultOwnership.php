<?php

namespace App\Entity\VaultOwnership;

use App\Entity\OrganizationGroup;
use Doctrine\ORM\Mapping as ORM;
use TypeError;

/**
 * @ORM\Entity()
 */
class OrganizationGroupVaultOwnership extends VaultOwnership
{
    /**
     * @var OrganizationGroup
     * @ORM\OneToOne(targetEntity="App\Entity\OrganizationGroup", inversedBy="vaultOwnership", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $group;

    public function __construct(OrganizationGroup $group = null)
    {
        parent::__construct();
        $this->group = $group;
    }

    public function getOwner(): VaultOwnerInterface
    {
        return $this->group;
    }

    public function setOwner(?VaultOwnerInterface $owner): VaultOwnership
    {
        if ($owner && !$owner instanceof OrganizationGroup) {
            throw new TypeError();
        }
        $this->group = $owner;

        return $this;
    }
}
