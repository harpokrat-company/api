<?php


namespace App\Entity\SecretOwnership;

use App\Entity\OrganizationGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OrganizationGroupSecretOwnership extends SecretOwnership
{
    /**
     * @var OrganizationGroup
     * @ORM\OneToOne(targetEntity="App\Entity\OrganizationGroup", inversedBy="secretOwnership", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $group;

    public function __construct(OrganizationGroup $group = null)
    {
        parent::__construct();
        $this->group = $group;
    }

    function getOwner(): SecretOwnerInterface
    {
        return $this->group;
    }

    function setOwner(?SecretOwnerInterface $owner): SecretOwnership
    {
        if ($owner && !$owner instanceof OrganizationGroup) {
            throw new \TypeError();
        }
        $this->group = $owner;
        return $this;
    }
}