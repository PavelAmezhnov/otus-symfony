<?php

namespace App\Controller\Api\v1\Skill;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Skill\Input\CreateData;
use App\Controller\Api\v1\Skill\Input\ReadData;
use App\Controller\Api\v1\Skill\Input\UpdateData;
use App\Entity\Skill;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\SkillManager;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/skill')]
class SkillController extends AbstractController
{

    public function __construct(
        private readonly SkillManager $skillManager,
        private readonly SkillRepository $skillRepository
    ) {

    }

    /**
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Skill
    {
        return $this->skillManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Skill
    {
        return $this->skillManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->skillManager->delete($id);
    }

    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->skillRepository->getSkills($dto);
    }
}
