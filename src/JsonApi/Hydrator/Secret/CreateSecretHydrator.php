<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Secret;

/**
 * Create Secret Hydrator.
 */
class CreateSecretHydrator extends AbstractSecretHydrator
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
