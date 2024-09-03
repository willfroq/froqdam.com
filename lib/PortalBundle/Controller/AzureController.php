<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AzureController extends AbstractController
{
    use TargetPathTrait;

    #[Route(path: 'connect/azure', name: 'connect_azure_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient(key: 'azure')
            ->redirect(scopes: ['openid', 'profile', 'user.Read', 'email'], options: []);
    }

    #[Route(path: 'connect/azure/check', name: 'connect_azure_check')]
    public function connectCheckAction(Request $request): RedirectResponse
    {
        $session = $request->getSession();

        $targetPath = $this->getTargetPath($session, 'portal');

        if ($targetPath) {
            return $this->redirect($targetPath);
        }

        $defaultRouteName = !is_array($this->getParameter('default_portal_dashboard_path')) ? $this->getParameter('default_portal_dashboard_path') : '';

        return $this->redirectToRoute((string) $defaultRouteName);
    }
}
