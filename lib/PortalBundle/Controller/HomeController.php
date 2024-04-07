<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use MembersBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function indexAction(): Response
    {
        $route = $this->getParameter('default_portal_dashboard_path');

        return $this->redirectToRoute(route: is_array($route) ? '' : (string) $route);
    }
}
