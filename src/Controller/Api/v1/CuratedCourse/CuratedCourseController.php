<?php

namespace App\Controller\Api\v1\CuratedCourse;

use App\Controller\Api\v1\CuratedCourse\Input\CreateData;
use App\Entity\CuratedCourse;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\CuratedCourseManager;
use App\Manager\StaffManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/curated-course')]
class CuratedCourseController extends AbstractController
{

    public function __construct(
        private readonly CuratedCourseManager $curatedCourseManager
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): CuratedCourse
    {
        $this->denyAccessUnlessGranted(StaffManager::ROLE_STAFF);
        return $this->curatedCourseManager->create($dto);
    }
}
