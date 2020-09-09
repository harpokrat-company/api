<?php

namespace App\Security;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OrganizationGroupVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(OrganizationGroup::class);
    }

    protected function attributeDefault($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        if (!$user = $token->getUser()) {
            return false;
        }

        return $this->isOwner($subject, $user) || $this->isOrganizationMember($subject, $user);
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
        $organizationMember = function ($subject, TokenInterface $token) {
            /** @var User $user */
            if (!$user = $token->getUser()) {
                return false;
            }

            return $this->isOwner($subject, $user) || $this->isOrganizationMember($subject, $user);
        };
        $members = function ($subject, TokenInterface $token) {
            /** @var User $user */
            if (!$user = $token->getUser()) {
                return false;
            }

            return $this->isOwner($subject, $user) || $this->isMember($subject, $user);
        };

        return [
            'create' => function ($subject, TokenInterface $token) {
                return $token->getUser() instanceof User;
            }, /* TODO : fix this */
            'edit' => $owner,
            'edit-secret' => $members,
            'edit-vaults' => $members,
            'delete' => $owner,
            'view' => $organizationMember,
            'view-secrets' => $members,
            'view-vaults' => $members,
        ];
    }

    private function isOwner(OrganizationGroup $subject, User $user): bool
    {
        return $subject->getOrganization()->getOwner() === $user;
    }

    private function isMember(OrganizationGroup $subject, User $user): bool
    {
        return $subject->getMembers()->contains($user);
    }

    private function isOrganizationMember(OrganizationGroup $subject, User $user): bool
    {
        return $subject->getOrganization()->getOwner() === $user;
    }
}
