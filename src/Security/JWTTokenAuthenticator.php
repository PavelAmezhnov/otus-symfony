<?php

namespace App\Security;

use App\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Throwable;

class JWTTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly JWTEncoderInterface $jwtEncoder)
    {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    /**
     * @throws InvalidTokenException
     */
    public function authenticate(Request $request): Passport
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if ($token === null) {
            throw new InvalidTokenException('No JWT token was provided');
        }

        try {
            $tokenData = $this->jwtEncoder->decode($token);
            if (!isset($tokenData['login'])) {
                throw new InvalidTokenException('Invalid JWT token');
            }
        } catch (Throwable $e) {
            throw new InvalidTokenException($e->getMessage());
        }

        return new SelfValidatingPassport(
            new UserBadge($tokenData['login'], fn() => new AuthUser($tokenData))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['message' => 'Invalid JWT Token'], Response::HTTP_FORBIDDEN);
    }
}
