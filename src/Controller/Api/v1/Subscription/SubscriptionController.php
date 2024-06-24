<?php

namespace App\Controller\Api\v1\Subscription;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Subscription\Input\CreateData;
use App\Controller\Api\v1\Subscription\Input\ReadData;
use App\Controller\Api\v1\Subscription\Input\UpdateData;
use App\Entity\Subscription;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StaffManager;
use App\Manager\SubscriptionManager;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/v1/subscription')]
class SubscriptionController extends AbstractController
{

    public function __construct(
        private readonly SubscriptionManager $subscriptionManager,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * @throws EntityNotFoundException|BadRequestException|AccessDeniedException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto, UserInterface $user): Subscription
    {
        return $this->subscriptionService->subscribe($dto, $user);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Subscription
    {
        $this->denyAccessUnlessGranted(StaffManager::ROLE_ADMIN);
        return $this->subscriptionManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id, UserInterface $user)
    {
        return $this->subscriptionService->delete($id, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto, UserInterface $user): EntityCollection
    {
        return $this->subscriptionService->getSubscriptions($dto, $user);
    }
}
