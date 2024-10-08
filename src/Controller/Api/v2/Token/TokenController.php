<?php

namespace App\Controller\Api\v2\Token;

use App\Service\AuthService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v2/token')]
class TokenController extends AbstractController
{

    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function getTokenAction(Request $request): JsonResponse
    {
        $user = $request->getUser();
        $password = $request->getPassword();
        if (!$user || !$password) {
            return new JsonResponse(['message' => 'Authorization required'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->authService->isCredentialsValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid password or username'], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(['token' => $this->authService->getJWTToken($user)]);
    }
}
