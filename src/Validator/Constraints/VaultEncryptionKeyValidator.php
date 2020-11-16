<?php

namespace App\Validator\Constraints;

use App\Entity\Vault;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class VaultEncryptionKeyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof VaultEncryptionKey) {
            throw new UnexpectedTypeException($constraint, VaultEncryptionKey::class);
        }
        if (!$value instanceof Vault) {
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
