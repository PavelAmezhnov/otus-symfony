<?php

namespace App\Controller\Api\v1\User;

use App\Controller\Api\v1\User\Input\CreateData;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/user')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly UserManager $userManager
    )
    {

    }

    /**
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): User
    {
        return $this->userManager->create($dto);
    }
}
