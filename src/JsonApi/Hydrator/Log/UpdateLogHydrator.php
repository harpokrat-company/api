<?php

namespace App\JsonApi\Hydrator\Log;

use App\Entity\Log;

/**
 * Update Log Hydrator.
 */
class UpdateLogHydrator extends AbstractLogHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($log): array
    {
        return [
            'date' => function (Log $log, $attribute, $data, $attributeName) {
                $log->setDate(new \DateTime($attribute));
            },
            'uri' => function (Log $log, $attribute, $data, $attributeName) {
                $log->setUri($attribute);
            },
            'ip' => function (Log $log, $attribute, $data, $attributeName) {
                $log->setIp($attribute);
            },
        ];
    }
}
