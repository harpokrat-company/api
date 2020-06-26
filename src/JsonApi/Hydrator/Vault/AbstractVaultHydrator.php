<?php


namespace App\JsonApi\Hydrator\Vault;


use App\Entity\Vault;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractVaultHydrator extends AbstractHydrator
{
    use ResourceHydratorTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateClientGeneratedId(
        string $clientGeneratedId,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): void {
        if (!empty($clientGeneratedId)) {
            throw $exceptionFactory->createClientGeneratedIdNotSupportedException(
                $request,
                $clientGeneratedId
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function generateId(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['vaults'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($vault): array
    {
        return [
            'name' => function (Vault $vault, $attribute, $data, $attributeName) {
                $vault->setName($attribute);
            },
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Paknahad\JsonApiBundle\Exception\InvalidAttributeException
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(Vault::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($vault, string $id): void
    {
        if ($id && (string) $vault->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($vault): array
    {
        return [
        ];
    }
}