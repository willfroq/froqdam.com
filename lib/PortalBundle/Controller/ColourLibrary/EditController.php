<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\ColourLibrary;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edit', name: 'froq_portal.colour_library.', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class EditController extends AbstractController
{
    #[Route('/{id}', name: 'edit', methods: [Request::METHOD_GET])]
    public function search(Request $request): Response
    {
        return $this->render(view: '@FroqPortalBundle/colour-library/edit.html.twig', parameters: [
            'user' => $this->getUser(),
            'itemsLayout' => 'list',
            'brand_name' => 'Amstel',
            'product_name' => 'Amstel Lager',
        ]);
    }
}

