<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\ColourLibrary;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/detail', name: 'froq_portal.colour_library', methods: [Request::METHOD_GET])]
class DetailController extends AbstractController
{
    #[Route('/{id}', name: 'search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->render(view: '@FroqPortalBundle/colour-library/detail.html.twig');
    }
}
