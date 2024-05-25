<?php

namespace App\Controller\Api\v1\Subscription;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Subscription\Input\CreateData;
use App\Controller\Api\v1\Subscription\Input\ReadData;
use App\Controller\Api\v1\Subscription\Input\UpdateData;
use App\Entity\Subscription;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\SubscriptionManager;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/subscription')]
class SubscriptionController extends AbstractController
{

    public function __construct(
        private readonly SubscriptionManager $subscriptionManager,
        private readonly SubscriptionRepository $subscriptionRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Subscription
    {
        return $this->subscriptionManager->subscribe($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Subscription
    {
        return $this->subscriptionManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->subscriptionManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->subscriptionRepository->getSubscriptions($dto);
    }
}
