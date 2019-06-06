<?php

namespace App\Controller;

use App\Provider\SecretProviderInterface;
use App\Repository\SecretRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SecretController extends AbstractJsonApiController
{
    public function create(Request $request, SecretProviderInterface $secretProvider)
    {
        if ($request->getContentType() !== 'json')
            throw new BadRequestHttpException('Incorrect json'); // TODO
        return $this->jsonApiResponseProvider->createResponse($secretProvider->createSecret($this->getUser(),
            json_decode($request->getContent(), true)['data']['attributes']['content']));
    }

    public function getResource(Request $request, int $id, SecretRepository $secretRepository)
    {
        $secret = $secretRepository->find($id);
        if ($this->getUser() !== $secret->getOwner())
            throw new UnauthorizedHttpException('Unauthorized');
        return $this->jsonApiResponseProvider->createResponse($secret);
    }

    public function update(Request $request, int $id, SecretProviderInterface $secretProvider,
                           SecretRepository $secretRepository)
    {
        $secret = $secretRepository->find($id);
        if ($this->getUser() !== $secret->getOwner())
            throw new UnauthorizedHttpException('Unauthorized');
        if ($request->getContentType() !== 'json')
            throw new BadRequestHttpException('Incorrect json'); // TODO
        $secretProvider->update($secret, json_decode($request->getContent(), true)['data']['attributes']['content']);
        return $this->jsonApiResponseProvider->createResponse($secret);
    }

    public function list(Request $request, SecretRepository $secretRepository)
    {
        return $this->jsonApiResponseProvider->createResponse($secretRepository->findBy(
            ['owner' => $this->getUser()]));
    }
}
