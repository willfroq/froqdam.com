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
        $defaultRouteName = !is_array($this->getParameter('default_portal_dashboard_path')) ? $this->getParameter('default_portal_dashboard_path') : '';

        $session = $request->getSession();

        $targetPath = $this->getTargetPath($session, 'portal');

        if ($targetPath) {
            $urlComponents = parse_url($targetPath);
            $path = $urlComponents['path'] ?? '';
            $query = urldecode($urlComponents['query'] ?? '');

            $fullPath = $path . ($query ? '?' . $query : '');

            $fetchFormResultRegex = '/^\/portal\/asset-library\/search\/fetch-form-and-results\/?(?:\?.*)?$/';
            $loadMoreResultRegex = '/^\/portal\/asset-library\/search\/load-more\/?(?:\?.*)?$/';

            if (preg_match($fetchFormResultRegex, $fullPath) || preg_match($loadMoreResultRegex, $fullPath)) {
                $this->removeTargetPath($session, 'portal');

                return $this->redirectToRoute((string) $defaultRouteName);
            }

            $searchPageRegex = '/^\/portal\/asset-library\/search\/?(?:\?.*)?$/';
            $detailPageRegex = '/^\/portal\/asset-library\/detail\/\d+\/?(?:\?.*)?$/';

            if (preg_match($searchPageRegex, $fullPath) || preg_match($detailPageRegex, $fullPath)) {
                $this->removeTargetPath($session, 'portal');

                return $this->redirect($targetPath);
            }

            // Handle colour library paths
            $colourSearchPageRegex = '/^\/portal\/colour-library\/search\/?(?:\?.*)?$/';
            $colourDetailPageRegex = '/^\/portal\/colour-library\/detail\/\d+\/?(?:\?.*)?$/';
            $colourEditPageRegex = '/^\/portal\/colour-library\/edit\/\d+\/?(?:\?.*)?$/';

            if (preg_match($colourSearchPageRegex, $fullPath) || preg_match($colourDetailPageRegex, $fullPath) || preg_match($colourEditPageRegex, $fullPath)) {
                $this->removeTargetPath($session, 'portal');

                return $this->redirect($targetPath);
            }
        }

        return $this->redirectToRoute((string) $defaultRouteName);
    }
}
