<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class OrganizationGroupChildrenOrganization extends Constraint
{
    public $message = 'the {{id}} group is not from the organization';

    public function validatedBy()
    {
        return OrganizationGroupChildrenOrganizationValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
