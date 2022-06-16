<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private ApiTokenRepository $apiTokenRepo;
    private UserRepository $user;

    public function __construct(ApiTokenRepository $apiTokenRepo, UserRepository $user)
    {
        $this->apiTokenRepo = $apiTokenRepo;
        $this->user = $user;
    }

    public function supports(Request $request): bool
    {
        // look for header "Authorization: Bearer <token>"
        return $request->headers->has('Authorization')
            && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $authHeader = $request->headers->get('Authorization');
        $token = substr($authHeader, 7);

        $apiToken = $this->apiTokenRepo->findOneBy(['token' => $token]);

        if (empty($apiToken)) {
            throw new CustomUserMessageAuthenticationException('Invalid API Token');
        }

        $user = $this->user->findOneBy(['id' => $apiToken->getUserId()->getId()]);

        return new Passport(
            new UserBadge($user->getId()),
            new CustomCredentials(
                fn($credentials, UserInterface $user) => !$credentials->isExpired(),
                $apiToken
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessageKey()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
