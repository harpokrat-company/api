<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class OrganizationGroupMembersOrganization extends Constraint
{
    public $message = 'the user {{id}} is not a member of the organization';

    public function validatedBy()
    {
        return OrganizationGroupMembersOrganizationValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
