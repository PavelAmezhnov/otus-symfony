<?php

namespace App\Controller\Api\v1\Percentage;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Percentage\Input\CreateData;
use App\Controller\Api\v1\Percentage\Input\ReadData;
use App\Controller\Api\v1\Percentage\Input\UpdateData;
use App\Entity\Percentage;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\PercentageManager;
use App\Repository\PercentageRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/percentage')]
class PercentageController extends AbstractController
{

    public function __construct(
        private readonly PercentageManager $percentageManager,
        private readonly PercentageRepository $percentageRepository
    ) {

    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Percentage
    {
        return $this->percentageManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Percentage
    {
        return $this->percentageManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->percentageManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->percentageRepository->getPercentages($dto);
    }
}
