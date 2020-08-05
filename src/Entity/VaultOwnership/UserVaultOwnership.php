<?php


namespace App\Entity\VaultOwnership;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use TypeError;

/**
 * @ORM\Entity()
 */
class UserVaultOwnership extends VaultOwnership
{
    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="vaultOwnership", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    public function __construct(User $organizationGroup = null)
    {
        parent::__construct();
        $this->user = $organizationGroup;
    }

    function getOwner(): VaultOwnerInterface
    {
        return $this->user;
    }

    function setOwner(?VaultOwnerInterface $owner): VaultOwnership
    {
        if ($owner && !$owner instanceof User) {
            throw new TypeError();
        }
        $this->user = $owner;
        return $this;
    }
}