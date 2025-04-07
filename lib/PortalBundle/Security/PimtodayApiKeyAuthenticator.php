<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Security;

use Pimcore\Model\DataObject\ApiToken;
use Pimcore\Model\DataObject\ApiToken\Listing;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class PimtodayApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct()
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function getCredentials(Request $request): ?string
    {
        return $request->headers->get('X-AUTH-TOKEN');
    }

    /**
     * @throws \Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (null === $credentials) {
            return null;
        }

        $tokenListing = ApiToken::getByUuid($credentials);

        if (!($tokenListing instanceof Listing)) {
            return null;
        }

        $token = $tokenListing->current();

        if (!($token instanceof ApiToken)) {
            return null;
        }

        $organization = $token->getOrganization();

        if (!($organization instanceof Organization)) {
            return null;
        }

        $user = current($organization->getUsers());

        return $user instanceof User ? $user : null;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = ['message' => 'Authentication Required'];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }
}
