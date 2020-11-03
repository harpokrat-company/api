<?php

namespace App\Validator\Constraints;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OrganizationGroupMembersOrganizationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OrganizationGroupMembersOrganization) {
            throw new UnexpectedTypeException($constraint, OrganizationGroupMembersOrganization::class);
        }
        if (!$value instanceof OrganizationGroup) {
            throw new UnexpectedTypeException($value, OrganizationGroup::class);
        }

        $organizationMembers = $value->getOrganization()->getMembers();

        /** @var User $member */
        foreach ($value->getMembers() as $member) {
            if (!$organizationMembers->contains($member)) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value->getId())
                    ->setParameter('{{id}}', $member->getId())
                    ->addViolation();
            }
        }
    }
}
