<?php

namespace App\Validator\Constraints;

use App\Entity\OrganizationGroup;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OrganizationGroupChildrenOrganizationValidator extends ConstraintValidator
{
    /**
     * @param OrganizationGroup $value
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OrganizationGroupChildrenOrganization) {
            throw new UnexpectedTypeException($constraint, OrganizationGroupChildrenOrganization::class);
        }
        if (!$value instanceof OrganizationGroup) {
            throw new UnexpectedTypeException($value, OrganizationGroup::class);
        }

        /** @var OrganizationGroup $child */
        foreach ($value->getChildren() as $child) {
            if ($child->getOrganization() !== $value->getOrganization()) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value->getId())
                    ->setParameter('{{id}}', $child->getId())
                    ->addViolation();
            }
        }
    }
}
