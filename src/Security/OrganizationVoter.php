<?php

namespace App\Security;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OrganizationVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(Organization::class);
    }

    protected function attributeDefault($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        if (!$user = $token->getUser()) {
            return false;
        }

        return $this->isOwner($subject, $user) || $this->isMember($subject, $user);
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

            return $this->isOwner($subject, $user) || $this->isMember($subject, $user);
        };

        return [
            'create' => function ($subject, TokenInterface $token) {
                return $token->getUser() instanceof User;
            },
            'edit' => $owner,
            'view-groups' => $member,
            'delete' => $owner,
        ];
    }

    private function isOwner(Organization $subject, User $user): bool
    {
        return $subject->getOwner() === $user;
    }

    private function isMember(Organization $subject, User $user): bool
    {
        return $subject->getMembers()->contains($user);
    }
}
