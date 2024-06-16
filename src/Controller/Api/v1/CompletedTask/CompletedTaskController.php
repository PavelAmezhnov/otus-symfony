<?php

namespace App\Controller\Api\v1\CompletedTask;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\CompletedTask\Input\CreateData;
use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use App\Controller\Api\v1\CompletedTask\Input\UpdateData;
use App\Entity\CompletedTask;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Form\Type\CompletedTask\CreateType;
use App\Form\Type\CompletedTask\UpdateType;
use App\Manager\CompletedTaskManager;
use App\Repository\CompletedTaskRepository;
use App\Service\CompletedTaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/api/v1/completed-task')]
class CompletedTaskController extends AbstractController
{

    public function __construct(
        private readonly CompletedTaskService $completedTaskService,
        private readonly CompletedTaskManager $completedTaskManager,
        private readonly FormFactoryInterface $formFactory,
        private readonly CompletedTaskRepository $completedTaskRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws Throwable
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): CompletedTask
    {
        return $this->completedTaskManager->create($dto);
    }

    #[Route(path: '/create', methods: ['GET', 'POST'])]
    public function createFromForm(Request $request): Response
    {
        $form = $this->formFactory->create(CreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = CreateData::fromFormData($form->getData());

            try {
                $completedTask = $this->completedTaskManager->create($dto);

                return $this->redirectToRoute('view', ['id' => $completedTask->getId()]);
            } catch (Throwable $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('CompletedTask/create_form.html.twig', ['form' => $form]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): CompletedTask
    {
        return $this->completedTaskManager->update($dto);
    }

    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], methods: ['GET', 'PATCH'])]
    public function updateFromForm(Request $request, int $id): Response
    {
        $form = null;
        try {
            $completedTask = $this->completedTaskRepository->find($id);
            if ($completedTask === null) {
                throw new EntityNotFoundException('Completed task not found');
            }

            $dto = UpdateData::fromEntity($completedTask);
            $form = $this->formFactory->create(UpdateType::class, $dto);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->completedTaskManager->update($dto);

                return $this->redirectToRoute('view', ['id' => $completedTask->getId()]);
            }
        } catch (Throwable $e) {
            $form = $form === null ? $this->formFactory->create(UpdateType::class) : $form;
            $form->addError(new FormError( $e->getMessage()));
        }

        return $this->render('CompletedTask/edit_form.html.twig', ['form' => $form]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->completedTaskManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): int|float|EntityCollection
    {
        return $this->completedTaskService->read($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', name: 'view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id): array
    {
        return $this->completedTaskService->readById($id);
    }
}
