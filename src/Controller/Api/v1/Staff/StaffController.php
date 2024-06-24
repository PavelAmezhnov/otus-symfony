<?php

namespace App\Controller\Api\v1\Staff;

use App\Controller\Api\v1\Staff\Input\CreateData;
use App\Entity\Staff;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StaffManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/staff')]
class StaffController extends AbstractController
{

    public function __construct(
        private readonly StaffManager $staffManager
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Staff
    {
        $this->denyAccessUnlessGranted(StaffManager::ROLE_ADMIN);
        return $this->staffManager->create($dto);
    }
}
