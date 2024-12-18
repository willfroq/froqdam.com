<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use MembersBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class HomeController extends AbstractController
{
    use TargetPathTrait;

    public function indexAction(Request $request): Response
    {
        $routeName = !is_array($this->getParameter('default_portal_dashboard_path')) ? $this->getParameter('default_portal_dashboard_path') : '';

        if (!$request->isXmlHttpRequest()) {
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

                    return $this->redirectToRoute((string) $routeName);
                }

                $searchPageRegex = '/^\/portal\/asset-library\/search\/?(?:\?.*)?$/';
                $detailPageRegex = '/^\/portal\/asset-library\/detail\/\d+\/?(?:\?.*)?$/';

                if (preg_match($searchPageRegex, $fullPath) || preg_match($detailPageRegex, $fullPath)) {
                    $this->removeTargetPath($session, 'portal');

                    return $this->redirect($targetPath);
                }
            }
        }

        return $this->redirectToRoute((string) $routeName);
    }
}
