<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\ColourLibrary;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'froq_portal.colour_library.', methods: [Request::METHOD_GET])]
class SearchController extends AbstractController
{
    #[Route('/', name: 'search', methods: [Request::METHOD_GET])]
    public function search(Request $request): Response
    {
        return $this->render('@FroqPortalBundle/colour-library/search.html.twig', [
            'user' => $this->getUser(),
            'itemsLayout' => 'list',
        ]);
    }
}
