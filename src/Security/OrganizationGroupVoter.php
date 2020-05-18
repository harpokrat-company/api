<?php


namespace App\Security;


use App\Entity\OrganizationGroup;
use App\Entity\User;

class OrganizationGroupVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(OrganizationGroup::class);
    }

    protected function canView($subject, User $user)
    {
        /** @var OrganizationGroup $group */
        $group = $subject;
        $organization = $group->getOrganization();

        # TODO : implements secret groups
        return $user === $organization->getOwner() || in_array($user, $organization->getMembers()->toArray());
    }

    protected function canEdit($subject, User $user)
    {
        /** @var OrganizationGroup $group */
        $group = $subject;
        $organization = $group->getOrganization();

        # TODO : implements group administrator
        return $user === $organization->getOwner();
    }

    protected function canDelete($subject, User $user)
    {
        /** @var OrganizationGroup $group */
        $group = $subject;
        $organization = $group->getOrganization();

        # TODO : implements group administrator
        return $user === $organization->getOwner();
    }
}