<?php

namespace App\JsonApi\Hydrator\SecureAction;

use App\Entity\SecureAction;

/**
 * Update SecureAction Hydrator.
 */
class UpdateSecureActionHydrator extends AbstractSecureActionHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($secureAction): array
    {
        return [
            'validated' => function (SecureAction $secureAction, $attribute, $data, $attributeName) {
                if ($attribute) {
                    $secureAction->setValidated(true);
                }
            },
        ];
    }
}
