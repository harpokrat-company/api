<?php

namespace App\Security;

use App\Entity\OrganizationGroup;
use App\Entity\Secret;
use App\Entity\User;
use App\Entity\Vault;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SecretVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(Secret::class);
    }

    protected function getAttributesFunctions(): array
    {
        $owner = function ($subject, TokenInterface $token) {
            /** @var User $user */
            if (!$user = $token->getUser()) {
                return false;
            }

            return $this->isOwner($subject, $user);
        };
        $private = function ($subject, TokenInterface $token) {
            /** @var User $user */
            if (!$user = $token->getUser()) {
                return false;
            }

            return $subject->isPrivate ? $this->isMember($subject, $user) : true;
        };

        return [
            'create' => function ($subject, TokenInterface $token) {
                return $token->getUser() instanceof User;
            }, /* TODO : fix this */
            'view' => $private,
            'edit' => $owner,
            'delete' => $owner,
        ];
    }

    public function isOwner(Secret $secret, User $user)
    {
        $owner = $secret->getOwner();
        if ($owner instanceof User) {
            return $owner === $user;
        }
        if ($owner instanceof OrganizationGroup) {
            return $owner->getOrganization()->getOwner() === $user;
        }
        if ($owner instanceof Vault) {
            $ownerOwner = $owner->getOwner();
            if ($ownerOwner instanceof User) {
                return $ownerOwner === $user;
            }
            if ($ownerOwner instanceof OrganizationGroup) {
                return $ownerOwner->getOrganization()->getOwner() === $user;
            }
        }

        return false;
    }

    public function isMember(Secret $secret, User $user)
    {
        $owner = $secret->getOwner();
        if ($owner instanceof User) {
            return $owner === $user;
        }
        if ($owner instanceof OrganizationGroup) {
            return $owner->getMembers()->contains($user);
        }
        if ($owner instanceof Vault) {
            $ownerOwner = $owner->getOwner();
            if ($ownerOwner instanceof User) {
                return $ownerOwner === $user;
            }
            if ($ownerOwner instanceof OrganizationGroup) {
                return $ownerOwner->getMembers()->contains($user);
            }
        }

        return false;
    }
}
