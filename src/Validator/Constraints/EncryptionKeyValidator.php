<?php

namespace App\Validator\Constraints;

use App\Entity\Secret;
use App\Entity\Vault;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EncryptionKeyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EncryptionKey) {
            throw new UnexpectedTypeException($constraint, EncryptionKey::class);
        }
        if (!$value instanceof Secret) {
            throw new UnexpectedTypeException($value, Vault::class);
        }

        if (!$value->isEncryptionKey()) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value->getId())
                ->setParameter('{{id}}', $value->getId())
                ->addViolation();
        }
    }
}
