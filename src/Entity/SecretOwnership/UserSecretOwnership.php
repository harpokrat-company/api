<?php

namespace App\Entity\SecretOwnership;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserSecretOwnership extends SecretOwnership
{
    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="secretOwnership")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    public function __construct(User $organizationGroup = null)
    {
        parent::__construct();
        $this->user = $organizationGroup;
    }

    public function getOwner(): SecretOwnerInterface
    {
        return $this->user;
    }

    public function setOwner(?SecretOwnerInterface $owner): SecretOwnership
    {
        if ($owner && !$owner instanceof User) {
            throw new \TypeError();
        }
        $this->user = $owner;

        return $this;
    }
}
