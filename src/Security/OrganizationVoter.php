<?php


namespace App\Security;


use App\Entity\Organization;
use App\Entity\User;

class OrganizationVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(Organization::class);
    }

    protected function canView($subject, User $user)
    {
        /** @var Organization $organization */
        $organization = $subject;

        return $user === $organization->getOwner() || in_array($user, $organization->getMembers()->toArray());
    }

    protected function canEdit($subject, User $user)
    {
        /** @var Organization $organization */
        $organization = $subject;

        # TODO : implements team administrator
        return $user === $organization->getOwner();
    }

    protected function canDelete($subject, User $user)
    {
        /** @var Organization $organization */
        $organization = $subject;

        return $user === $organization->getOwner();
    }
}