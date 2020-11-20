<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EncryptionKey extends Constraint
{
    public $message = 'the {{sid}} secret is not in the {{vid}} vault';

    public function validatedBy()
    {
        return EncryptionKeyValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
