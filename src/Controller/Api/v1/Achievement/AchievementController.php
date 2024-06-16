<?php

namespace App\Controller\Api\v1\Achievement;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Achievement\Input\CreateData;
use App\Controller\Api\v1\Achievement\Input\ReadData;
use App\Controller\Api\v1\Achievement\Input\UpdateData;
use App\Entity\Achievement;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Form\Type\Achievement\CreateType;
use App\Form\Type\Achievement\UpdateType;
use App\Manager\AchievementManager;
use App\Repository\AchievementRepository;
use App\Service\AchievementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/api/v1/achievement')]
class AchievementController extends AbstractController
{

    public function __construct(
        private readonly AchievementManager $achievementManager,
        private readonly AchievementRepository $achievementRepository,
        private readonly AchievementService $achievementService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws Throwable
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Achievement
    {
        return $this->achievementManager->create($dto);
    }

    #[Route(path: '/create', methods: ['GET', 'POST'])]
    public function createFromForm(Request $request): Response
    {
        $form = $this->formFactory->create(CreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = CreateData::fromFormData($form->getData());
            try {
                $achievement = $this->achievementManager->create($dto);

                return $this->redirectToRoute('view', ['id' => $achievement->getId()]);
            } catch (Throwable $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('Achievement/form.html.twig', ['form' => $form]);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->achievementRepository->getAchievements($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', name: 'view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id): array
    {
        return $this->achievementService->readById($id);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Achievement
    {
        return $this->achievementManager->update($dto);
    }

    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], methods: ['GET', 'PATCH'])]
    public function updateFromForm(Request $request, int $id): Response
    {
        $form = null;
        try {
            $achievement = $this->achievementRepository->find($id);
            if ($achievement === null) {
                throw new EntityNotFoundException('Achievement not found');
            }

            $dto = UpdateData::fromEntity($achievement);
            $form = $this->formFactory->create(UpdateType::class, $dto);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->achievementManager->update($dto);

                return $this->redirectToRoute('view', ['id' => $achievement->getId()]);
            }
        } catch (Throwable $e) {
            $form = $form === null ? $this->formFactory->create(UpdateType::class) : $form;
            $form->addError(new FormError($e->getMessage()));
        }

        return $this->render('Achievement/form.html.twig', ['form' => $form]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->achievementManager->delete($id);
    }
}
