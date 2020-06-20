<?php


namespace App\Validator\Constraints;


use App\Entity\OrganizationGroup;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OrganizationGroupChildrenRecursionValidator extends ConstraintValidator
{
    private function getChild(OrganizationGroup $group, array $parent = [])
    {
        $parent[] = $group->getId();
        /** @var OrganizationGroup $child */
        foreach ($group->getChildren() as $child) {
            if (in_array($child->getId(), $parent)) {
                return true;
            }
            if ($this->getChild($child, $parent)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param OrganizationGroup $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OrganizationGroupChildrenRecursion) {
            throw new UnexpectedTypeException($constraint, OrganizationGroupChildrenRecursion::class);
        }
        if (!$value instanceof OrganizationGroup) {
            throw new UnexpectedTypeException($value, OrganizationGroup::class);
        }

        while ($parent = $value->getParent()) {
            $value = $parent;
        }

        if ($this->getChild($value)) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value->getId())
                ->addViolation();
        }
    }
}