<?php

namespace App\Security;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Entity\Vault;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class VaultVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(Vault::class);
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
        $member = function ($subject, TokenInterface $token) {
            /** @var User $user */
            if (!$user = $token->getUser()) {
                return false;
            }

            return $this->isMember($subject, $user);
        };

        return [
            'create' => function ($subject, TokenInterface $token) {
                return $token->getUser() instanceof User;
            }, /* TODO : fix this */
            'view' => $member,
            'edit' => $owner,
            'edit-secrets' => $member,
            'delete' => $owner,
        ];
    }

    public function isOwner(Vault $vault, User $user)
    {
        $owner = $vault->getOwner();
        if ($owner instanceof User) {
            return $owner === $user;
        }
        if ($owner instanceof OrganizationGroup) {
            return $owner->getOrganization()->getOwner() === $user;
        }

        return false;
    }

    public function isMember(Vault $vault, User $user)
    {
        $owner = $vault->getOwner();
        if ($owner instanceof User) {
            return $owner === $user;
        }
        if ($owner instanceof OrganizationGroup) {
            return $owner->getMembers()->contains($user);
        }

        return false;
    }
}
