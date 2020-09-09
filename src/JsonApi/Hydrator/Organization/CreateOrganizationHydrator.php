<?php


namespace App\JsonApi\Hydrator\Organization;


use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateOrganizationHydrator extends AbstractOrganizationHydrator
{
    protected function getContext(): string
    {
        return self::CREATION;
    }

    protected function getRelationshipHydrator($organization): array
    {
        return [
            'groups' => function (Organization $organization, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
            'members' => function (Organization $organization, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var User[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                foreach ($members as $member) {
                    $organization->addMember($member);
                }
            },
            'owner' => function (Organization $organization, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var User $owner */
                $owner = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                if (!$this->authorizationChecker->isGranted('edit-organizations', $owner)) {
                    throw new AccessDeniedHttpException();
                }
                $organization->setOwner($owner);
                $organization->addMember($owner);
            },
        ];
    }

}