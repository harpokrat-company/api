<?php

namespace App\Validator\Constraints;

use App\Entity\EncryptionKeyInterface;
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
        if (!$value instanceof EncryptionKeyInterface) {
            throw new UnexpectedTypeException($value, Vault::class);
        }

        $ek = $value->getEncryptionKey();
        if (null !== $ek && $value !== $ek->getOwner()) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value->getId())
                ->setParameter('{{sid}}', $ek->getId())
                ->setParameter('{{vid}}', $value->getId())
                ->addViolation();
        }
    }
}
