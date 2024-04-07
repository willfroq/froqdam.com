<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AzureController extends AbstractController
{
    #[Route(path: 'connect/azure', name: 'connect_azure_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient(key: 'azure')
            ->redirect(scopes: ['openid', 'profile', 'user.Read', 'email'], options: []);
    }

    #[Route(path: 'connect/azure/check', name: 'connect_azure_check')]
    public function connectCheckAction(): RedirectResponse
    {
        return $this->redirectToRoute(route: 'froq_portal.asset_library.search');
    }
}
