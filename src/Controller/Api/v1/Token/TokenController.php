<?php

namespace App\Controller\Api\v1\Token;

use App\Service\AuthService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/token')]
class TokenController extends AbstractController
{

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function getTokenAction(): JsonResponse
    {
        return new JsonResponse(['message' => "Method's deprecated"], Response::HTTP_GONE);
    }
}
