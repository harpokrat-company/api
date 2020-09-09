<?php


namespace App\JsonApi\Hydrator\User;


class CreateRelationshipUserHydrator extends AbstractUserHydrator
{
    protected function getContext(): string
    {
        return self::CREATION;
    }
}