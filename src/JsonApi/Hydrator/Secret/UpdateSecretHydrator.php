<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Secret;

/**
 * Update Secret Hydrator.
 */
class UpdateSecretHydrator extends AbstractSecretHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($secret): array
    {
        return [
            'content' => function (Secret $secret, $attribute, $data, $attributeName) {
                $secret->setContent($attribute);
            },
        ];
    }
}
