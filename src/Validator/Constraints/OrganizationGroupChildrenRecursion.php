<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class OrganizationGroupChildrenRecursion extends Constraint
{
    public $message = 'trying to create a group recursion';

    public function validatedBy()
    {
        return OrganizationGroupChildrenRecursionValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
