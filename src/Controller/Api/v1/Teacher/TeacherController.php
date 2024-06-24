<?php

namespace App\Controller\Api\v1\Teacher;

use App\Controller\Api\v1\Teacher\Input\CreateData;
use App\Entity\Teacher;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\TeacherManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/v1/teacher')]
class TeacherController extends AbstractController
{

    public function __construct(
        private readonly TeacherManager $teacherManager
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Teacher
    {
        return $this->teacherManager->create($dto);
    }
}
