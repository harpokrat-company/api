<?php


namespace App\Entity\SecretOwnership;

use App\Entity\Vault;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class VaultSecretOwnership extends SecretOwnership
{
    /**
     * @var Vault
     * @ORM\OneToOne(targetEntity="App\Entity\Vault", inversedBy="secretOwnership", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $vault;

    public function __construct(Vault $organizationGroup = null)
    {
        parent::__construct();
        $this->vault = $organizationGroup;
    }

    function getOwner(): SecretOwnerInterface
    {
        return $this->vault;
    }

    function setOwner(?SecretOwnerInterface $owner): SecretOwnership
    {
        if ($owner && !$owner instanceof Vault) {
            throw new \TypeError();
        }
        $this->vault = $owner;
        return $this;
    }
}