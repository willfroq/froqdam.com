<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Security;

use Froq\PortalBundle\Action\CreateUserFromAzurePayload;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

class AzureAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(private readonly ClientRegistry $clientRegistry, private readonly CreateUserFromAzurePayload $createUserFromAzurePayload)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_azure_check';
    }

    public function getCredentials(Request $request): ResourceOwnerInterface
    {
        $client = $this->clientRegistry->getClient('azure');

        return $client->fetchUser();
    }

    /**
     * @throws \Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        /** @var AzureResourceOwner $azureUser */
        $azureUser = $credentials;

        $existingUser = User::getByEmail($azureUser->getUpn())?->current(); /** @phpstan-ignore-line */
        if ($existingUser instanceof User) {
            return $existingUser;
        }

        return ($this->createUserFromAzurePayload)($azureUser);
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
