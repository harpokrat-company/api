<?php

namespace App\JsonApi\Hydrator\SecureAction;

use App\Entity\SecureAction;

/**
 * Create SecureAction Hydrator.
 */
class CreateSecureActionHydrator extends AbstractSecureActionHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($secureAction): array
    {
        return [
            'actionType' => function (SecureAction $secureAction, $attribute, $data, $attributeName) {
                $secureAction->setActionType($attribute);
            },
            'payload' => function (SecureAction $secureAction, $attribute, $data, $attributeName) {
                $secureAction->setPayload($attribute);
            },
        ];
    }
}
